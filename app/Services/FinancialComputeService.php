<?php

namespace App\Services;

use App\Models\Area;
use App\Models\BudgetBucket;
use App\Models\BudgetBucketAmount;
use App\Models\GlTransaction;
use App\Models\StartingCashBalance;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialComputeService
{
    // ── Actuals ─────────────────────────────────────────────────────

    /**
     * Get the actual amount for a single bucket / area / year.
     * Prefers GL-computed sum; falls back to manual_actual.
     */
    public function getActuals(int $bucketId, int $areaId, int $year): float
    {
        $amount = BudgetBucketAmount::where('budget_bucket_id', $bucketId)
            ->where('area_id', $areaId)
            ->where('fiscal_year', $year)
            ->first();

        if (! $amount) {
            return 0;
        }

        // If source is gl_computed, the GL total is authoritative
        if ($amount->source === 'gl_computed') {
            return $this->computeGlTotal($bucketId, $areaId, $year);
        }

        return (float) ($amount->manual_actual ?? 0);
    }

    /**
     * Compute the GL-sourced total for a bucket by summing transactions
     * for all GL accounts mapped to that bucket.
     */
    public function computeGlTotal(int $bucketId, int $areaId, int $year): float
    {
        return (float) GlTransaction::query()
            ->join('gl_accounts', 'gl_transactions.gl_account_id', '=', 'gl_accounts.id')
            ->where('gl_accounts.budget_bucket_id', $bucketId)
            ->where('gl_transactions.area_id', $areaId)
            ->whereYear('gl_transactions.transaction_date', $year)
            ->sum('gl_transactions.amount');
    }

    // ── Summary Rows ────────────────────────────────────────────────

    /**
     * Evaluate summary formulas for all summary buckets.
     * Returns [bucket_id => computed_value].
     */
    public function computeSummaryRows(int $areaId, int $year, array $rowValues = []): array
    {
        $summaryBuckets = BudgetBucket::active()->ordered()->summaries()->get();
        $allBuckets = BudgetBucket::active()->ordered()->get();

        // First pass: fill non-summary values
        if (empty($rowValues)) {
            foreach ($allBuckets as $bucket) {
                if (! $bucket->is_summary) {
                    $rowValues[$bucket->id] = $this->getActuals($bucket->id, $areaId, $year);
                }
            }
        }

        // Second pass: evaluate summary formulas in sort order
        foreach ($summaryBuckets as $bucket) {
            $rowValues[$bucket->id] = $this->evaluateFormula(
                $bucket->summary_formula,
                $allBuckets,
                $rowValues
            );
        }

        return $rowValues;
    }

    /**
     * Evaluate a summary formula string.
     *
     * Supported formats:
     *   "sum:revenue"  — sum all non-summary buckets in the revenue category
     *   "sum:program"  — sum all non-summary buckets in the program category
     *   "row:total_income - row:total_opex"  — arithmetic on semantic keys
     *   "row:cogs + row:total_program + row:total_admin" — multi-term
     */
    public function evaluateFormula(
        ?string $formula,
        Collection $allBuckets,
        array $rowValues
    ): float {
        if (! $formula) {
            return 0;
        }

        // Handle "sum:category"
        if (str_starts_with($formula, 'sum:')) {
            $category = substr($formula, 4);

            return $allBuckets
                ->filter(fn ($b) => $b->category === $category && ! $b->is_summary)
                ->sum(fn ($b) => $rowValues[$b->id] ?? 0);
        }

        // Handle arithmetic expressions with "row:semantic_key"
        // Build a map of semantic_key => value
        $keyMap = [];
        foreach ($allBuckets as $bucket) {
            if ($bucket->semantic_key) {
                $keyMap[$bucket->semantic_key] = $rowValues[$bucket->id] ?? 0;
            }
        }

        // Replace row:key with numeric values
        $expression = preg_replace_callback('/row:(\w+)/', function ($matches) use ($keyMap) {
            return (string) ($keyMap[$matches[1]] ?? 0);
        }, $formula);

        // Safely evaluate simple arithmetic (only +, -, numbers, spaces, dots)
        $expression = trim($expression);
        if (preg_match('/^[\d\s\.\+\-]+$/', $expression)) {
            // Use a simple token-based evaluator
            return $this->safeEval($expression);
        }

        return 0;
    }

    /**
     * Safely evaluate a simple arithmetic expression (+ and - only).
     */
    private function safeEval(string $expression): float
    {
        $tokens = preg_split('/\s*([\+\-])\s*/', $expression, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $result = (float) array_shift($tokens);

        while (count($tokens) >= 2) {
            $operator = array_shift($tokens);
            $operand = (float) array_shift($tokens);

            if ($operator === '+') {
                $result += $operand;
            } elseif ($operator === '-') {
                $result -= $operand;
            }
        }

        return $result;
    }

    // ── P&L Array Builder ───────────────────────────────────────────

    /**
     * Build the P&L array for one area, in the exact format the dashboard JS expects:
     * [{label, isTotal, "2023": val, "2024": val, ...}, ...]
     */
    public function buildPnlArray(int $areaId, array $years): array
    {
        $buckets = BudgetBucket::active()->ordered()->get();

        // Load all amounts for this area at once
        $amounts = BudgetBucketAmount::where('area_id', $areaId)
            ->whereIn('fiscal_year', $years)
            ->get()
            ->groupBy('fiscal_year');

        $rows = [];
        foreach ($years as $year) {
            $yearAmounts = $amounts->get($year, collect());
            $amountLookup = $yearAmounts->keyBy('budget_bucket_id');

            // Get non-summary actuals
            $rowValues = [];
            foreach ($buckets as $bucket) {
                if (! $bucket->is_summary) {
                    $amountRecord = $amountLookup->get($bucket->id);
                    if ($amountRecord && $amountRecord->source === 'gl_computed') {
                        $rowValues[$bucket->id] = $this->computeGlTotal($bucket->id, $areaId, $year);
                    } else {
                        $rowValues[$bucket->id] = (float) ($amountRecord->manual_actual ?? 0);
                    }
                }
            }

            // Compute summaries
            $rowValues = $this->computeSummaryRows($areaId, $year, $rowValues);

            // Store year column values
            foreach ($buckets as $bucket) {
                $rows[$bucket->id][(string) $year] = $rowValues[$bucket->id] ?? 0;
            }
        }

        // Build final array
        $result = [];
        foreach ($buckets as $bucket) {
            $row = [
                'label'   => $bucket->name,
                'isTotal' => $bucket->is_summary,
            ];
            foreach ($years as $year) {
                $row[(string) $year] = $rows[$bucket->id][(string) $year] ?? 0;
            }
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Build the full pnl_by_area structure:
     * { "All Areas": [...], "Chippewa Valley": [...], ... }
     */
    public function buildPnlByArea(Collection $areas, array $years): array
    {
        $result = [];

        // Build per-area
        foreach ($areas as $area) {
            $result[$area->name] = $this->buildPnlArray($area->id, $years);
        }

        // Build "All Areas" by summing
        $buckets = BudgetBucket::active()->ordered()->get();
        $allAreasRows = [];
        foreach ($buckets as $i => $bucket) {
            $row = [
                'label'   => $bucket->name,
                'isTotal' => $bucket->is_summary,
            ];
            foreach ($years as $year) {
                $sum = 0;
                foreach ($areas as $area) {
                    $sum += $result[$area->name][$i][(string) $year] ?? 0;
                }
                $row[(string) $year] = $sum;
            }
            $allAreasRows[] = $row;
        }

        return array_merge(['All Areas' => $allAreasRows], $result);
    }

    // ── Semantic Key Index Map ───────────────────────────────────────

    /**
     * Build pnl_keys map: { "total_income": 8, "cogs": 9, "net_income": 18, ... }
     * Maps semantic_key → array index in the P&L array.
     */
    public function buildPnlKeys(): array
    {
        $buckets = BudgetBucket::active()->ordered()->get();
        $keys = [];

        foreach ($buckets->values() as $index => $bucket) {
            if ($bucket->semantic_key) {
                $keys[$bucket->semantic_key] = $index;
            }
        }

        return $keys;
    }

    // ── Efficiency KPIs ─────────────────────────────────────────────

    /**
     * Compute efficiency KPIs from bucket actuals + mission data.
     * Returns the same structure as the old efficiency_metrics table.
     */
    public function computeEfficiency(int $areaId, int $year): array
    {
        $buckets = BudgetBucket::active()->ordered()->get();
        $pnl = $this->buildPnlArray($areaId, [$year]);
        $pnlKeys = $this->buildPnlKeys();

        $totalIncome = $pnl[$pnlKeys['total_income'] ?? 8][(string) $year] ?? 0;
        $cogs = $pnl[$pnlKeys['cogs'] ?? 9][(string) $year] ?? 0;
        $totalOpex = $pnl[$pnlKeys['total_opex'] ?? 16][(string) $year] ?? 0;
        $netIncome = $pnl[$pnlKeys['net_income'] ?? 18][(string) $year] ?? 0;

        // Mission data for the area/year
        $mission = DB::table('mission_metrics')
            ->where('area_id', $areaId)
            ->where('fiscal_year', $year)
            ->first();

        $families = $mission ? (float) $mission->avg_monthly_families : 0;

        return [
            'fundraising_roi'    => $cogs > 0 ? round($totalIncome / $cogs, 2) : 0,
            'cost_per_family'    => $families > 0 ? round($totalOpex / ($families * 12), 2) : 0,
            'program_efficiency' => $totalOpex > 0 ? round(($pnl[$pnlKeys['total_program'] ?? 14][(string) $year] ?? 0) / $totalOpex * 100, 1) : 0,
            'admin_ratio'        => $totalOpex > 0 ? round(($pnl[$pnlKeys['total_admin'] ?? 15][(string) $year] ?? 0) / $totalOpex * 100, 1) : 0,
            'net_margin'         => $totalIncome > 0 ? round($netIncome / $totalIncome * 100, 1) : 0,
            'revenue_per_family' => $families > 0 ? round($totalIncome / ($families * 12), 2) : 0,
        ];
    }

    // ── Running Cash ────────────────────────────────────────────────

    /**
     * Compute running cash balance: starting_cash + cumulative net income per year.
     * Returns { "2023": end_balance, "2024": end_balance, ... }
     */
    public function computeRunningCash(int $areaId, array $years): array
    {
        $startingCash = StartingCashBalance::where('area_id', $areaId)->first();
        $balance = $startingCash ? (float) $startingCash->balance : 0;

        $pnl = $this->buildPnlArray($areaId, $years);
        $pnlKeys = $this->buildPnlKeys();
        $netIncomeIdx = $pnlKeys['net_income'] ?? 18;

        $result = [];
        sort($years);

        foreach ($years as $year) {
            $yearNetIncome = $pnl[$netIncomeIdx][(string) $year] ?? 0;
            $balance += $yearNetIncome;
            $result[(string) $year] = round($balance, 2);
        }

        return $result;
    }

    /**
     * Build starting_cash map for all areas: { "Chippewa Valley": 1234.56, ... }
     */
    public function buildStartingCashMap(): array
    {
        return StartingCashBalance::with('area')
            ->get()
            ->mapWithKeys(fn ($scb) => [$scb->area->name => (float) $scb->balance])
            ->toArray();
    }

    // ── Budget Comparison ───────────────────────────────────────────

    /**
     * Build budget vs actual comparison for one area/year.
     * Returns [{bucket_name, budget, actual, variance, variance_pct}, ...]
     */
    public function buildBudgetComparison(int $areaId, int $year): array
    {
        $buckets = BudgetBucket::active()->ordered()->get();

        $amounts = BudgetBucketAmount::where('area_id', $areaId)
            ->where('fiscal_year', $year)
            ->get()
            ->keyBy('budget_bucket_id');

        $pnl = $this->buildPnlArray($areaId, [$year]);
        $result = [];

        foreach ($buckets->values() as $index => $bucket) {
            $amountRecord = $amounts->get($bucket->id);
            $budget = $amountRecord ? (float) ($amountRecord->budget_amount ?? 0) : 0;
            $actual = $pnl[$index][(string) $year] ?? 0;
            $variance = $actual - $budget;
            $variancePct = $budget != 0 ? round($variance / abs($budget) * 100, 1) : 0;

            $result[] = [
                'bucket_name'  => $bucket->name,
                'category'     => $bucket->category,
                'is_summary'   => $bucket->is_summary,
                'budget'       => $budget,
                'actual'       => $actual,
                'variance'     => $variance,
                'variance_pct' => $variancePct,
            ];
        }

        return $result;
    }

    // ── Years Helper ────────────────────────────────────────────────

    /**
     * Get the distinct fiscal years available in bucket amounts.
     */
    public function getAvailableYears(): array
    {
        return BudgetBucketAmount::distinct()
            ->pluck('fiscal_year')
            ->sort()
            ->values()
            ->toArray();
    }
}
