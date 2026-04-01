<?php

namespace App\Services;

use App\Models\Area;
use App\Models\AreaAlias;
use App\Models\GlAccount;
use App\Models\GlImport;
use App\Models\GlTransaction;
use App\Models\PnlLineItem;
use App\Models\PnlValue;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

class GlImportService
{
    private array $aliasCache = [];
    private array $accountCache = [];
    private int $newAccountCount = 0;

    /**
     * Import a QBO General Ledger XLSX file.
     */
    public function import(string $filePath, int $fiscalYear, int $userId): GlImport
    {
        $this->loadAliasCache();
        $this->loadAccountCache();
        $this->newAccountCount = 0;

        $import = GlImport::create([
            'filename' => basename($filePath),
            'fiscal_year' => $fiscalYear,
            'status' => 'processing',
            'imported_by' => $userId,
        ]);

        try {
            DB::beginTransaction();

            // Delete existing transactions for this fiscal year to avoid duplicates
            GlTransaction::where('fiscal_year', $fiscalYear)->delete();

            // Increase memory limit for large XLSX files
            $prevMemoryLimit = ini_get('memory_limit');
            ini_set('memory_limit', '512M');

            $reader = new XlsxReader();
            $reader->setReadDataOnly(true); // Skip formatting, save memory
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $transactions = $this->parseRows($sheet, $fiscalYear, $import->id);

            // Free spreadsheet memory before bulk inserts
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $sheet);
            ini_set('memory_limit', $prevMemoryLimit);

            // Bulk insert transactions in chunks
            $matched = 0;
            $unmatched = 0;

            foreach (array_chunk($transactions, 500) as $chunk) {
                GlTransaction::insert($chunk);
                foreach ($chunk as $txn) {
                    if ($txn['area_id']) {
                        $matched++;
                    } else {
                        $unmatched++;
                    }
                }
            }

            $import->update([
                'total_rows' => count($transactions),
                'matched_rows' => $matched,
                'unmatched_rows' => $unmatched,
                'new_accounts' => $this->newAccountCount,
                'status' => 'completed',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $import->update([
                'status' => 'failed',
                'error_log' => $e->getMessage(),
            ]);
        }

        return $import->fresh();
    }

    /**
     * Parse all rows from the QBO GL sheet.
     */
    private function parseRows($sheet, int $fiscalYear, int $importId): array
    {
        $transactions = [];
        $accountStack = [];
        $currentAccount = null;
        $now = now()->format('Y-m-d H:i:s');

        $highestRow = $sheet->getHighestRow();

        for ($rowIndex = 1; $rowIndex <= $highestRow; $rowIndex++) {
            $colA = trim((string) ($sheet->getCell("A{$rowIndex}")->getValue() ?? ''));
            $colB = trim((string) ($sheet->getCell("B{$rowIndex}")->getValue() ?? ''));

            // Skip completely empty rows and header rows (first 5 rows)
            if ($rowIndex <= 5) continue;
            if ($colA === '' && $colB === '') continue;

            // Account header: col A has text, col B is empty
            if ($colA !== '' && $colB === '') {
                if ($this->isTotalRow($colA)) {
                    // Pop the account stack
                    if (!empty($accountStack)) {
                        array_pop($accountStack);
                        $currentAccount = end($accountStack) ?: null;
                    }
                    continue;
                }

                // New account section
                [$number, $name] = $this->parseAccountHeader($colA);
                if ($number === '' && $name === '') continue;

                $parentId = $currentAccount?->id;
                $depth = count($accountStack);
                $account = $this->findOrCreateAccount($number, $name, $parentId, $depth);

                $accountStack[] = $account;
                $currentAccount = $account;
                continue;
            }

            // Transaction row: col B has text and we have a current account
            if ($colB !== '' && $currentAccount) {
                $date = $sheet->getCell("C{$rowIndex}")->getValue();
                $type = $sheet->getCell("D{$rowIndex}")->getValue();
                $num = $sheet->getCell("E{$rowIndex}")->getValue();
                $name = $sheet->getCell("F{$rowIndex}")->getValue();
                $memo = $sheet->getCell("G{$rowIndex}")->getValue();
                $split = $sheet->getCell("H{$rowIndex}")->getValue();
                $amount = $sheet->getCell("I{$rowIndex}")->getValue();
                $balance = $sheet->getCell("J{$rowIndex}")->getValue();

                // Skip "Beginning Balance" rows
                if ($colB === 'Beginning Balance') continue;

                // Parse the transaction date
                $txnDate = $this->parseDate($date, $fiscalYear);
                if (!$txnDate) continue;

                // Parse amount
                $amountVal = $this->parseAmount($amount);
                if ($amountVal === null) continue;

                // Extract area from memo
                $memoStr = $memo !== null ? trim((string) $memo) : null;
                $areaRaw = $this->extractAreaFromMemo($memoStr);
                $areaId = $this->resolveArea($areaRaw);

                $transactions[] = [
                    'gl_account_id' => $currentAccount->id,
                    'gl_import_id' => $importId,
                    'area_id' => $areaId,
                    'fiscal_year' => $fiscalYear,
                    'transaction_date' => $txnDate,
                    'type' => $type !== null ? trim((string) $type) : null,
                    'num' => $num !== null ? trim((string) $num) : null,
                    'name' => $name !== null ? trim((string) $name) : null,
                    'memo' => $memoStr,
                    'split_account' => $split !== null ? trim((string) $split) : null,
                    'amount' => $amountVal,
                    'balance' => $balance !== null ? round((float) $balance, 2) : null,
                    'memo_area_raw' => $areaRaw,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        return $transactions;
    }

    /**
     * Check if a row is a "Total for..." closing row.
     */
    private function isTotalRow(string $text): bool
    {
        return str_starts_with($text, 'Total for ');
    }

    /**
     * Parse account header like "3010 Individual Donations" into [number, name].
     */
    private function parseAccountHeader(string $text): array
    {
        // Skip report title rows
        if (str_starts_with($text, 'General Ledger')
            || str_starts_with($text, 'Safe Families')
            || str_contains($text, 'Accrual Basis')
            || preg_match('/^(January|February|March|April|May|June|July|August|September|October|November|December)\s/', $text)) {
            return ['', ''];
        }

        // Try to parse "1234 Account Name" or "12345 Account Name"
        if (preg_match('/^(\d+)\s+(.+)$/', $text, $m)) {
            return [trim($m[1]), trim($m[2])];
        }

        // Account without a number (e.g., "Retained Earnings", "Short-Term Lease Liability")
        // Generate a slug-based number
        $slug = 'X-' . substr(md5($text), 0, 6);
        return [$slug, trim($text)];
    }

    /**
     * Extract area text from pipe-delimited memo field.
     * Revenue format: "Donor Name | Area | Category"
     * Some expense format: "Employee Name | Area/Amount"
     */
    private function extractAreaFromMemo(?string $memo): ?string
    {
        if (empty($memo)) return null;

        $parts = array_map('trim', explode('|', $memo));
        if (count($parts) < 2) return null;

        $candidate = $parts[1];

        // Skip if it looks like a number (expense memos often have amounts in position 2)
        if (is_numeric($candidate)) return null;

        // Skip if empty
        if ($candidate === '') return null;

        // Skip if it looks like a long number (phone, ID)
        if (preg_match('/^\d{5,}$/', $candidate)) return null;

        return $candidate;
    }

    /**
     * Resolve area text to area_id via alias cache.
     */
    private function resolveArea(?string $areaText): ?int
    {
        if ($areaText === null) return null;

        // Normalize: trim and handle "Kenosha/ Racine" vs "Kenosha/Racine"
        $normalized = trim($areaText);

        if (isset($this->aliasCache[$normalized])) {
            return $this->aliasCache[$normalized];
        }

        // Try without extra spaces around slashes
        $compact = preg_replace('/\s*\/\s*/', '/', $normalized);
        if (isset($this->aliasCache[$compact])) {
            return $this->aliasCache[$compact];
        }

        return null;
    }

    /**
     * Find or create a GL account.
     */
    private function findOrCreateAccount(string $number, string $name, ?int $parentId, int $depth): GlAccount
    {
        if (isset($this->accountCache[$number])) {
            return $this->accountCache[$number];
        }

        $accountType = $this->inferAccountType($number);

        $account = GlAccount::firstOrCreate(
            ['account_number' => $number],
            [
                'account_name' => $name,
                'account_type' => $accountType,
                'parent_account_id' => $parentId,
                'depth' => $depth,
                'is_active' => true,
            ]
        );

        if ($account->wasRecentlyCreated) {
            $this->newAccountCount++;
        }

        $this->accountCache[$number] = $account;
        return $account;
    }

    /**
     * Infer account type from the account number.
     */
    private function inferAccountType(string $number): string
    {
        if (!is_numeric($number)) return 'other';

        $num = (int) $number;
        if ($num >= 3000 && $num < 4000) return 'revenue';
        if ($num >= 5000 && $num < 6000) return 'expense';
        if ($num >= 7000 && $num < 8000) return 'other'; // in-kind, rev sharing
        if ($num >= 1000 && $num < 2000) return 'asset';
        if ($num >= 2000 && $num < 3000) return 'liability';
        return 'other';
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate($value, int $fiscalYear): ?string
    {
        if ($value === null || $value === '') return null;

        // PhpSpreadsheet may return a date string like "01/02/2025"
        $str = trim((string) $value);

        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $str, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[1], $m[2]);
        }

        // If it's a numeric Excel serial date
        if (is_numeric($value)) {
            try {
                $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value);
                return $dateTime->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    /**
     * Parse amount value.
     */
    private function parseAmount($value): ?float
    {
        if ($value === null || $value === '') return null;
        if (is_numeric($value)) return round((float) $value, 2);
        // Try stripping currency formatting
        $cleaned = preg_replace('/[^0-9.\-]/', '', (string) $value);
        return is_numeric($cleaned) ? round((float) $cleaned, 2) : null;
    }

    /**
     * Load area alias cache.
     */
    private function loadAliasCache(): void
    {
        $this->aliasCache = AreaAlias::pluck('area_id', 'alias_text')->toArray();
    }

    /**
     * Load existing GL accounts cache.
     */
    private function loadAccountCache(): void
    {
        $this->accountCache = GlAccount::all()->keyBy('account_number')->toArray();
        // Convert to model instances
        $this->accountCache = GlAccount::all()->keyBy('account_number')->all();
    }

    /**
     * Recompute P&L values from GL transactions for a fiscal year.
     */
    public function recomputePnlValues(int $fiscalYear): array
    {
        $areas = Area::all();
        $lineItems = PnlLineItem::ordered()->get();
        $mappedAccounts = GlAccount::whereNotNull('pnl_line_item_id')->get();

        if ($mappedAccounts->isEmpty()) {
            return ['error' => 'No GL accounts are mapped to P&L line items yet.'];
        }

        $stats = ['updated' => 0, 'areas' => 0];

        DB::beginTransaction();

        foreach ($areas as $area) {
            // Sum GL transactions by mapped P&L line item for this area
            $sums = DB::table('gl_transactions')
                ->join('gl_accounts', 'gl_transactions.gl_account_id', '=', 'gl_accounts.id')
                ->where('gl_transactions.fiscal_year', $fiscalYear)
                ->where('gl_transactions.area_id', $area->id)
                ->whereNotNull('gl_accounts.pnl_line_item_id')
                ->groupBy('gl_accounts.pnl_line_item_id')
                ->selectRaw('gl_accounts.pnl_line_item_id, SUM(gl_transactions.amount) as total')
                ->pluck('total', 'pnl_line_item_id');

            if ($sums->isEmpty()) continue;

            $stats['areas']++;

            // Write individual (non-total) line items
            foreach ($sums as $lineItemId => $total) {
                PnlValue::updateOrCreate(
                    ['area_id' => $area->id, 'line_item_id' => $lineItemId, 'fiscal_year' => $fiscalYear],
                    ['amount' => round($total, 2), 'source' => 'gl_computed']
                );
                $stats['updated']++;
            }

            // Recompute summary/total rows
            $this->recomputeSummaryRows($area->id, $fiscalYear, $lineItems);
        }

        DB::commit();

        return $stats;
    }

    /**
     * Recompute summary/total P&L rows from their component rows.
     */
    private function recomputeSummaryRows(int $areaId, int $fiscalYear, $lineItems): void
    {
        // Get current values for this area/year
        $values = PnlValue::where('area_id', $areaId)
            ->where('fiscal_year', $fiscalYear)
            ->get()
            ->keyBy('line_item_id');

        $lineItemsBySort = $lineItems->keyBy('sort_order');

        $getVal = function (int $sortOrder) use ($lineItemsBySort, $values) {
            $li = $lineItemsBySort->get($sortOrder);
            if (!$li) return 0;
            $pv = $values->get($li->id);
            return $pv ? (float) $pv->amount : 0;
        };

        $setTotal = function (int $sortOrder, float $amount) use ($lineItemsBySort, $areaId, $fiscalYear) {
            $li = $lineItemsBySort->get($sortOrder);
            if (!$li) return;
            PnlValue::updateOrCreate(
                ['area_id' => $areaId, 'line_item_id' => $li->id, 'fiscal_year' => $fiscalYear],
                ['amount' => round($amount, 2), 'source' => 'gl_computed']
            );
        };

        // sort_order 8: TOTAL INCOME = sum of 0-7
        $totalIncome = 0;
        for ($i = 0; $i <= 7; $i++) {
            $totalIncome += $getVal($i);
        }
        $setTotal(8, $totalIncome);

        // sort_order 14: TOTAL PROGRAM COSTS = sum of 10-13
        $totalProgram = 0;
        for ($i = 10; $i <= 13; $i++) {
            $totalProgram += $getVal($i);
        }
        $setTotal(14, $totalProgram);

        // sort_order 15: TOTAL ADMIN COSTS (from GL directly, already set if mapped)
        // If not set from GL, compute as: Total OpEx - Program - Fundraising
        $adminCosts = $getVal(15);

        // sort_order 16: TOTAL OPERATING EXPENSES = Fundraising + Program + Admin
        $fundraising = $getVal(9);
        $totalOpex = $fundraising + $totalProgram + $adminCosts;
        $setTotal(16, $totalOpex);

        // sort_order 17: NET OPERATING INCOME = Total Income - Total OpEx
        $netOperating = $totalIncome - $totalOpex;
        $setTotal(17, $netOperating);

        // sort_order 18: NET INCOME (may include adjustments, use same as net operating for now)
        $setTotal(18, $netOperating);
    }

    /**
     * Get unmatched area names from transactions for a given fiscal year.
     */
    public function getUnmatchedAreas(int $fiscalYear): array
    {
        return DB::table('gl_transactions')
            ->where('fiscal_year', $fiscalYear)
            ->whereNull('area_id')
            ->whereNotNull('memo_area_raw')
            ->where('memo_area_raw', '!=', '')
            ->groupBy('memo_area_raw')
            ->selectRaw('memo_area_raw, COUNT(*) as count')
            ->orderByDesc('count')
            ->get()
            ->toArray();
    }

    /**
     * Auto-map GL accounts to P&L line items using known patterns.
     */
    public function autoMapAccounts(): int
    {
        $lineItems = PnlLineItem::ordered()->get()->keyBy('sort_order');
        $mapped = 0;

        // Define mapping: GL account number prefix => P&L sort_order
        $mappings = [
            '3010' => 0,  // Individual Donations
            '3015' => 1,  // Church Giving
            '3020' => 2,  // Corporate Giving
            '3025' => 3,  // Grants & Foundation
            '3035' => 3,  // Giving Fund/Foundation → same line
            '3105' => 6,  // Sponsorship Revenue → Event Tickets & Sponsorships
            '3110' => 6,  // Ticket Revenue → Event Tickets & Sponsorships
            '3200' => 7,  // Interest Revenue
            '3205' => 7,  // MKE Transportation Fund Interest → Interest Revenue
            '5305' => 9,  // Event Food & Drink → Fundraising Costs
            '5320' => 9,  // Event Venue → Fundraising Costs
            '5325' => 9,  // Other Fundraising Expense → Fundraising Costs
            '5326' => 9,  // Event Marketing → Fundraising Costs
            '5327' => 9,  // Event Supplies → Fundraising Costs
            '5328' => 9,  // Giving Platform Fees → Fundraising Costs
            '5329' => 9,  // Fundraising Meeting Expense → Fundraising Costs
            '5330' => 9,  // Fundraising Wages → Fundraising Costs
            '5335' => 9,  // Event Entertainment → Fundraising Costs
            '5345' => 9,  // Non Cash Prizes → Fundraising Costs
            '5050' => 10, // Program Staffing (parent)
            '5055' => 10, // Workman's Comp → Program Staffing
            '5060' => 10, // Wages → Program Staffing
            '5065' => 10, // Program Contractors → Program Staffing
            '5070' => 10, // Payroll Taxes → Program Staffing
            '5075' => 10, // Shared Staffing → Program Staffing
            '5080' => 10, // Employee Benefits → Program Staffing
            '5082' => 10, // Staff Development → Program Staffing
            '5083' => 10, // 401k Matching → Program Staffing
            '5084' => 10, // Life & Disability → Program Staffing
            '5085' => 10, // Health Benefits → Program Staffing
            '5110' => 11, // Community Engagement (parent)
            '5111' => 11, // Volunteer Recruiting → Community Engagement
            '5112' => 11, // Digital Marketing → Community Engagement
            '5113' => 11, // Print Marketing → Community Engagement
            '5114' => 11, // Sponsored Engagement → Community Engagement
            '5120' => 12, // Family Support (parent)
            '5121' => 12, // Direct Support → Family Support
            '5122' => 12, // Background Checks → Family Support
            '5124' => 12, // Training and Licensing → Family Support
            '5125' => 12, // Support Services → Family Support
            '5126' => 12, // Program Licensing → Family Support
            '5127' => 12, // Program Facility → Family Support
            '5128' => 12, // MKE Housing Fund → Family Support
            '5129' => 12, // MKE Transportation Fund → Family Support
            '5130' => 13, // Program Meeting Expense
            '5200' => 15, // Administrative Costs (parent) → TOTAL ADMIN
            '5205' => 15, // Liability Insurance → Admin
            '5206' => 15, // General Liability → Admin
            '5207' => 15, // Professional Liability → Admin
            '5210' => 15, // Office Expenses → Admin
            '5211' => 15, // Rent → Admin
            '5212' => 15, // Office Supplies → Admin
            '5213' => 15, // Other Office → Admin
            '5220' => 15, // Admin Tech → Admin
            '5230' => 15, // Admin Staffing → Admin
            '5231' => 15, // Admin Payroll Taxes → Admin
            '5232' => 15, // Admin Health Benefits → Admin
            '5233' => 15, // Admin 401k → Admin
            '5234' => 15, // Admin Life and Disability → Admin
            '5235' => 15, // Benefits Admin → Admin
            '5236' => 15, // Admin Development → Admin
            '5237' => 15, // Admin Wages → Admin
            '5238' => 15, // Admin Worker's Comp → Admin
            '5260' => 15, // WI Taxes → Admin
        ];

        foreach ($mappings as $accountNumber => $sortOrder) {
            $lineItem = $lineItems->get($sortOrder);
            if (!$lineItem) continue;

            $updated = GlAccount::where('account_number', $accountNumber)
                ->whereNull('pnl_line_item_id')
                ->update(['pnl_line_item_id' => $lineItem->id]);

            $mapped += $updated;
        }

        return $mapped;
    }
}
