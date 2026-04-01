<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BudgetBucketSeeder extends Seeder
{
    /**
     * Migrate data from old pnl_line_items / pnl_values / budgets tables
     * into the new budget_buckets / budget_bucket_amounts system.
     */
    public function run(): void
    {
        // ── 1. Create 19 budget buckets mirroring old P&L line items ───

        $buckets = [
            // sort_order => [name, category, is_summary, summary_formula, semantic_key]
            0  => ['Individual Donations', 'revenue', false, null, null],
            1  => ['Church Giving', 'revenue', false, null, null],
            2  => ['Corporate Giving', 'revenue', false, null, null],
            3  => ['Grants & Foundation', 'revenue', false, null, null],
            4  => ['DOA Grant Revenue', 'revenue', false, null, null],
            5  => ['Contract Revenue', 'revenue', false, null, null],
            6  => ['Event Tickets & Sponsorships', 'revenue', false, null, null],
            7  => ['Interest Revenue', 'revenue', false, null, null],
            8  => ['TOTAL INCOME', 'revenue', true, 'sum:revenue', 'total_income'],
            9  => ['Fundraising Costs (COGS)', 'cogs', false, null, 'cogs'],
            10 => ['Program Staffing', 'program', false, null, null],
            11 => ['Community Engagement', 'program', false, null, null],
            12 => ['Family Support', 'program', false, null, null],
            13 => ['Program Meetings', 'program', false, null, null],
            14 => ['TOTAL PROGRAM COSTS', 'program', true, 'sum:program', 'total_program'],
            15 => ['TOTAL ADMIN COSTS', 'admin', true, 'sum:admin', 'total_admin'],
            16 => ['TOTAL OPERATING EXPENSES', 'summary', true, 'row:cogs + row:total_program + row:total_admin', 'total_opex'],
            17 => ['NET OPERATING INCOME', 'summary', true, 'row:total_income - row:total_opex', 'net_operating'],
            18 => ['NET INCOME', 'summary', true, 'row:net_operating + row:rev_sharing', 'net_income'],
        ];

        // Map old pnl_line_items IDs by sort_order
        $oldLineItems = DB::table('pnl_line_items')
            ->orderBy('sort_order')
            ->pluck('id', 'sort_order')
            ->toArray();

        $bucketIds = []; // sort_order => new bucket id

        foreach ($buckets as $sortOrder => [$name, $category, $isSummary, $formula, $semanticKey]) {
            $legacyId = $oldLineItems[$sortOrder] ?? null;

            $bucketIds[$sortOrder] = DB::table('budget_buckets')->insertGetId([
                'name'                    => $name,
                'category'                => $category,
                'is_summary'              => $isSummary,
                'summary_formula'         => $formula,
                'semantic_key'            => $semanticKey,
                'sort_order'              => $sortOrder,
                'is_active'               => true,
                'legacy_pnl_line_item_id' => $legacyId,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);
        }

        $this->command->info('Created ' . count($bucketIds) . ' budget buckets.');

        // ── 2. Migrate pnl_values → budget_bucket_amounts (manual_actual) ─

        $pnlValues = DB::table('pnl_values')
            ->join('pnl_line_items', 'pnl_values.line_item_id', '=', 'pnl_line_items.id')
            ->select('pnl_values.*', 'pnl_line_items.sort_order')
            ->get();

        $amountCount = 0;
        foreach ($pnlValues as $pv) {
            $bucketId = $bucketIds[$pv->sort_order] ?? null;
            if (! $bucketId) {
                continue;
            }

            DB::table('budget_bucket_amounts')->updateOrInsert(
                [
                    'budget_bucket_id' => $bucketId,
                    'area_id'          => $pv->area_id,
                    'fiscal_year'      => $pv->fiscal_year,
                ],
                [
                    'manual_actual' => $pv->amount,
                    'source'        => 'manual',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            );
            $amountCount++;
        }

        $this->command->info("Migrated {$amountCount} P&L values → bucket amounts (manual_actual).");

        // ── 3. Migrate budgets → budget_bucket_amounts (budget_amount) ──

        // Budget columns → bucket sort_order mapping
        // The old budgets table has aggregate columns that map to specific P&L rows:
        $budgetColumnMap = [
            'individual_donations' => 0,   // Individual Donations
            'church_giving'        => 1,   // Church Giving
            // sort_order 2 (Corporate Giving) — no direct budget column
            'grant_revenue'        => 3,   // Grants & Foundation
            // sort_order 4 (DOA Grant Revenue) — no direct budget column
            // sort_order 5 (Contract Revenue) — no direct budget column
            'fundraising_events'   => 6,   // Event Tickets & Sponsorships
            // sort_order 7 (Interest Revenue) — no direct budget column
            'revenue'              => 8,   // TOTAL INCOME
            'cogs'                 => 9,   // Fundraising Costs (COGS)
            // sort_order 10-13 (individual program line items) — no direct budget column
            'program_costs'        => 14,  // TOTAL PROGRAM COSTS
            'admin_costs'          => 15,  // TOTAL ADMIN COSTS
            'total_expenses'       => 16,  // TOTAL OPERATING EXPENSES
            'net_operating'        => 17,  // NET OPERATING INCOME
            'net_revenue'          => 18,  // NET INCOME
        ];

        $budgets = DB::table('budgets')->get();
        $budgetCount = 0;

        foreach ($budgets as $budget) {
            foreach ($budgetColumnMap as $column => $sortOrder) {
                $value = $budget->$column;
                if ($value === null) {
                    continue;
                }

                $bucketId = $bucketIds[$sortOrder] ?? null;
                if (! $bucketId) {
                    continue;
                }

                DB::table('budget_bucket_amounts')->updateOrInsert(
                    [
                        'budget_bucket_id' => $bucketId,
                        'area_id'          => $budget->area_id,
                        'fiscal_year'      => $budget->fiscal_year,
                    ],
                    [
                        'budget_amount' => $value,
                        'updated_at'    => now(),
                    ]
                );
                $budgetCount++;
            }
        }

        $this->command->info("Migrated {$budgetCount} budget values → bucket amounts (budget_amount).");

        // ── 4. Copy gl_accounts pnl_line_item_id → budget_bucket_id ─────

        // Build legacy_pnl_line_item_id → budget_bucket_id mapping
        $legacyMap = DB::table('budget_buckets')
            ->whereNotNull('legacy_pnl_line_item_id')
            ->pluck('id', 'legacy_pnl_line_item_id')
            ->toArray();

        $glUpdated = 0;
        foreach ($legacyMap as $oldPnlId => $newBucketId) {
            $glUpdated += DB::table('gl_accounts')
                ->where('pnl_line_item_id', $oldPnlId)
                ->update(['budget_bucket_id' => $newBucketId]);
        }

        $this->command->info("Mapped {$glUpdated} GL accounts to budget buckets.");

        // ── 5. Seed starting_cash_balances from financial_snapshots ─────
        //
        //    Starting cash = equity (end of prior year).
        //    The earliest P&L data year is 2023, so starting cash = equity
        //    at end of FY2022. We don't have FY2022 snapshots, so we
        //    reverse-compute: equity_2025 - net_income_2025 - net_income_2024 - net_income_2023 ≈ starting_2022
        //    But actually we only have FY2025 equity. To compute the 2022 starting balance:
        //    starting_cash = equity_2025 - (cumulative net income from 2023 + 2024 + 2025)

        $areas = DB::table('areas')->pluck('id', 'name')->toArray();

        // Get the net_income bucket (sort_order 18)
        $netIncomeBucketId = $bucketIds[18];

        foreach ($areas as $areaName => $areaId) {
            // Get FY2025 equity
            $snapshot = DB::table('financial_snapshots')
                ->where('area_id', $areaId)
                ->where('fiscal_year', 2025)
                ->first();

            if (! $snapshot || $snapshot->equity === null) {
                continue;
            }

            // Sum net income for all years we have data
            $cumulativeNetIncome = DB::table('budget_bucket_amounts')
                ->where('budget_bucket_id', $netIncomeBucketId)
                ->where('area_id', $areaId)
                ->sum('manual_actual');

            $startingCash = round((float) $snapshot->equity - (float) $cumulativeNetIncome, 2);

            DB::table('starting_cash_balances')->updateOrInsert(
                ['area_id' => $areaId],
                [
                    'balance'    => $startingCash,
                    'as_of_date' => '2022-12-31',
                    'notes'      => 'Auto-computed: FY2025 equity (' . $snapshot->equity . ') minus cumulative net income (' . $cumulativeNetIncome . ')',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Seeded starting cash balances for ' . count($areas) . ' areas.');

        // ── 6. Set admin user role ──────────────────────────────────────

        $updated = DB::table('users')
            ->where('email', 'admin@sfcwi.org')
            ->update(['role' => 'admin']);

        if ($updated) {
            $this->command->info('Set admin@sfcwi.org role to admin.');
        }
    }
}
