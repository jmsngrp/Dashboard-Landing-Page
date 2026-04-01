<?php

namespace App\Services;

use App\Models\AreaAlias;
use App\Models\GlAccount;
use App\Models\GlImport;
use App\Models\GlTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QboSyncService
{
    private QboClient $client;
    private array $aliasCache = [];
    private array $accountCacheByQboId = [];
    private int $newAccountCount = 0;

    public function __construct(QboClient $client)
    {
        $this->client = $client;
    }

    // ── Main Sync ───────────────────────────────────────────────────

    /**
     * Sync GL transactions from QBO for a fiscal year or date range.
     */
    public function sync(
        int $fiscalYear,
        int $userId,
        ?string $startDate = null,
        ?string $endDate = null
    ): GlImport {
        $startDate = $startDate ?: "{$fiscalYear}-01-01";
        $endDate   = $endDate   ?: "{$fiscalYear}-12-31";

        $this->loadCaches();

        $import = GlImport::create([
            'filename'        => "QBO Sync {$startDate} to {$endDate}",
            'source'          => 'qbo_api',
            'fiscal_year'     => $fiscalYear,
            'sync_start_date' => $startDate,
            'sync_end_date'   => $endDate,
            'status'          => 'processing',
            'imported_by'     => $userId,
        ]);

        try {
            DB::beginTransaction();

            // Step 1: Sync QBO Chart of Accounts → local gl_accounts
            $this->syncAccounts();

            // Step 2: Delete existing transactions for this fiscal year
            // (same strategy as XLSX import — prevents duplicates)
            GlTransaction::where('fiscal_year', $fiscalYear)->delete();

            // Step 3: Pull GL transactions from QBO
            $transactions = $this->pullTransactions($fiscalYear, $startDate, $endDate, $import->id);

            // Step 4: Bulk insert in chunks
            $matched = 0;
            $unmatched = 0;

            foreach (array_chunk($transactions, 500) as $chunk) {
                GlTransaction::insert($chunk);
                foreach ($chunk as $txn) {
                    $txn['area_id'] ? $matched++ : $unmatched++;
                }
            }

            $import->update([
                'total_rows'    => count($transactions),
                'matched_rows'  => $matched,
                'unmatched_rows' => $unmatched,
                'new_accounts'  => $this->newAccountCount,
                'status'        => 'completed',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('QBO Sync failed', [
                'import_id' => $import->id,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);

            $import->update([
                'status'    => 'failed',
                'error_log' => $e->getMessage(),
            ]);
        }

        return $import->fresh();
    }

    // ── Account Sync ────────────────────────────────────────────────

    /**
     * Pull QBO Chart of Accounts and sync to local gl_accounts table.
     */
    private function syncAccounts(): void
    {
        $accounts = $this->client->query("SELECT * FROM Account MAXRESULTS 1000");

        foreach ($accounts as $qboAccount) {
            $qboId  = $qboAccount->Id;
            $name   = $qboAccount->Name;
            $number = $qboAccount->AcctNum ?? ('QBO-' . $qboId);
            $type   = $this->mapQboAccountType($qboAccount->AccountType ?? 'Other');

            // Handle sub-accounts
            $parentId = null;
            if (isset($qboAccount->ParentRef)) {
                $parentQboId = $qboAccount->ParentRef;
                $parentLocal = $this->accountCacheByQboId[$parentQboId] ?? null;
                $parentId = $parentLocal?->id;
            }

            $account = GlAccount::updateOrCreate(
                ['qbo_account_id' => (string) $qboId],
                [
                    'account_number'    => $number,
                    'account_name'      => $name,
                    'account_type'      => $type,
                    'parent_account_id' => $parentId,
                    'depth'             => (isset($qboAccount->SubAccount) && $qboAccount->SubAccount) ? 1 : 0,
                    'is_active'         => ($qboAccount->Active ?? 'true') === 'true',
                ]
            );

            if ($account->wasRecentlyCreated) {
                $this->newAccountCount++;
            }

            $this->accountCacheByQboId[(string) $qboId] = $account;
        }
    }

    // ── Transaction Pull ────────────────────────────────────────────

    /**
     * Pull transactions from QBO GeneralLedger report.
     */
    private function pullTransactions(
        int $fiscalYear,
        string $startDate,
        string $endDate,
        int $importId
    ): array {
        $report = $this->client->getReport('GeneralLedger', [
            'start_date'        => $startDate,
            'end_date'          => $endDate,
            'columns'           => 'tx_date,txn_type,doc_num,name,memo,account_name,split_acc,subt_nat_amount,rbal_nat_amount,klass_name',
            'accounting_method' => 'Accrual',
        ]);

        return $this->parseGeneralLedgerReport($report, $fiscalYear, $importId);
    }

    /**
     * Parse the QBO GeneralLedger report response into transaction arrays.
     *
     * The report structure uses nested Rows with Header (account group)
     * and Data (individual transaction) rows.
     */
    private function parseGeneralLedgerReport(array $report, int $fiscalYear, int $importId): array
    {
        $transactions = [];
        $now = now()->format('Y-m-d H:i:s');

        // Build column index from the Columns section
        $columns = $report['Columns']['Column'] ?? [];
        $colMap = [];
        foreach ($columns as $i => $col) {
            $colMap[$i] = $col['ColType'] ?? $col['ColTitle'] ?? "col_{$i}";
        }

        // Process nested row structure
        $rows = $report['Rows']['Row'] ?? [];
        $this->processRowSection($rows, null, $fiscalYear, $importId, $now, $transactions);

        return $transactions;
    }

    /**
     * Recursively process report row sections (accounts may nest).
     */
    private function processRowSection(
        array $rows,
        ?string $currentAccountQboId,
        int $fiscalYear,
        int $importId,
        string $now,
        array &$transactions
    ): void {
        foreach ($rows as $row) {
            $type = $row['type'] ?? '';

            // Section: contains Header + nested Rows
            if ($type === 'Section') {
                $accountQboId = $currentAccountQboId;

                // Extract account ID from Header
                if (isset($row['Header'])) {
                    $extractedId = $this->extractAccountIdFromHeader($row['Header']);
                    if ($extractedId) {
                        $accountQboId = $extractedId;
                    }
                }

                // Process nested rows within this section
                $nestedRows = $row['Rows']['Row'] ?? [];
                $this->processRowSection($nestedRows, $accountQboId, $fiscalYear, $importId, $now, $transactions);

                continue;
            }

            // Data row: an individual transaction
            if ($type === 'Data') {
                $txn = $this->parseDataRow($row, $currentAccountQboId, $fiscalYear, $importId, $now);
                if ($txn) {
                    $transactions[] = $txn;
                }
            }
        }
    }

    /**
     * Parse a single Data row into a transaction array.
     */
    private function parseDataRow(
        array $row,
        ?string $accountQboId,
        int $fiscalYear,
        int $importId,
        string $now
    ): ?array {
        $colData = $row['ColData'] ?? [];
        if (count($colData) < 8) {
            return null;
        }

        // Extract fields by column position (matching the columns= param order)
        $date      = $colData[0]['value'] ?? null;
        $type      = $colData[1]['value'] ?? null;
        $num       = $colData[2]['value'] ?? null;
        $name      = $colData[3]['value'] ?? null;
        $memo      = $colData[4]['value'] ?? null;
        // $accountName = $colData[5]['value'] ?? null; // account_name (redundant, we have QBO ID)
        $split     = $colData[6]['value'] ?? null;
        $amount    = $colData[7]['value'] ?? null;
        $balance   = $colData[8]['value'] ?? null;
        $className = $colData[9]['value'] ?? null;

        // Also check if this data row has an account ID embedded
        if (! $accountQboId && isset($colData[5]['id'])) {
            $accountQboId = (string) $colData[5]['id'];
        }

        if (! $date || $amount === null || $amount === '') {
            return null;
        }

        // Resolve local account
        $localAccount = $accountQboId
            ? ($this->accountCacheByQboId[(string) $accountQboId] ?? null)
            : null;

        if (! $localAccount) {
            return null; // Can't record without a GL account
        }

        // Parse amount
        $amountVal = $this->parseAmount($amount);
        if ($amountVal === null) {
            return null;
        }

        // Resolve area: QBO Class (primary) → memo extraction (fallback)
        $areaId = null;
        $areaRaw = null;

        if ($className && trim($className) !== '') {
            $areaRaw = trim($className);
            $areaId = $this->resolveArea($areaRaw);
        }

        if (! $areaId && $memo) {
            $memoArea = $this->extractAreaFromMemo($memo);
            if ($memoArea) {
                $areaRaw = $areaRaw ?: $memoArea;
                $areaId = $this->resolveArea($memoArea);
            }
        }

        return [
            'gl_account_id'    => $localAccount->id,
            'gl_import_id'     => $importId,
            'area_id'          => $areaId,
            'fiscal_year'      => $fiscalYear,
            'transaction_date' => $this->parseDate($date),
            'type'             => $type ? mb_substr(trim($type), 0, 50) : null,
            'num'              => $num ? mb_substr(trim($num), 0, 50) : null,
            'name'             => $name ? trim($name) : null,
            'memo'             => $memo ? trim($memo) : null,
            'split_account'    => $split ? trim($split) : null,
            'amount'           => $amountVal,
            'balance'          => $balance !== null ? $this->parseAmount($balance) : null,
            'memo_area_raw'    => $areaRaw,
            'created_at'       => $now,
            'updated_at'       => $now,
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function loadCaches(): void
    {
        $this->aliasCache = AreaAlias::pluck('area_id', 'alias_text')->toArray();
        $this->accountCacheByQboId = GlAccount::whereNotNull('qbo_account_id')
            ->get()
            ->keyBy('qbo_account_id')
            ->all();
        $this->newAccountCount = 0;
    }

    /**
     * Resolve area text to area_id via AreaAlias cache.
     * Mirrors GlImportService::resolveArea() logic.
     */
    private function resolveArea(?string $areaText): ?int
    {
        if ($areaText === null || trim($areaText) === '') {
            return null;
        }

        $normalized = trim($areaText);

        if (isset($this->aliasCache[$normalized])) {
            return $this->aliasCache[$normalized];
        }

        // Normalize spacing around slashes: "Kenosha/ Racine" → "Kenosha/Racine"
        $compact = preg_replace('/\s*\/\s*/', '/', $normalized);
        if (isset($this->aliasCache[$compact])) {
            return $this->aliasCache[$compact];
        }

        return null;
    }

    /**
     * Extract area from pipe-delimited memo (fallback for transactions without ClassRef).
     * Mirrors GlImportService::extractAreaFromMemo().
     */
    private function extractAreaFromMemo(?string $memo): ?string
    {
        if (empty($memo)) {
            return null;
        }

        $parts = array_map('trim', explode('|', $memo));
        if (count($parts) < 2) {
            return null;
        }

        $candidate = $parts[1];
        if ($candidate === '' || is_numeric($candidate)) {
            return null;
        }

        // Skip long numbers (phone numbers, IDs)
        if (preg_match('/^\d{5,}$/', $candidate)) {
            return null;
        }

        return $candidate;
    }

    /**
     * Parse date string. QBO API returns YYYY-MM-DD format.
     */
    private function parseDate(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        // QBO API standard: YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // Fallback: MM/DD/YYYY
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $value, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[1], $m[2]);
        }

        return null;
    }

    /**
     * Parse amount string, stripping currency formatting.
     */
    private function parseAmount(?string $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $clean = preg_replace('/[^0-9.\-]/', '', $value);

        return is_numeric($clean) ? round((float) $clean, 2) : null;
    }

    /**
     * Extract QBO account ID from a report Header row.
     */
    private function extractAccountIdFromHeader(array $header): ?string
    {
        $colData = $header['ColData'] ?? [];
        if (! empty($colData) && isset($colData[0]['id'])) {
            return (string) $colData[0]['id'];
        }

        return null;
    }

    /**
     * Map QBO AccountType string to our local type.
     */
    private function mapQboAccountType(string $qboType): string
    {
        return match ($qboType) {
            'Income', 'Revenue'                        => 'revenue',
            'Expense', 'Cost of Goods Sold'            => 'expense',
            'Bank', 'Accounts Receivable',
            'Other Current Asset', 'Fixed Asset',
            'Other Asset'                              => 'asset',
            'Accounts Payable', 'Credit Card',
            'Other Current Liability',
            'Long Term Liability'                      => 'liability',
            default                                    => 'other',
        };
    }
}
