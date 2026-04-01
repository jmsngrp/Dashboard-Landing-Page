<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Budget;
use App\Models\GlTransaction;
use App\Models\EfficiencyMetric;
use App\Models\ExpenseSummary;
use App\Models\FinancialSnapshot;
use App\Models\HighlightGroup;
use App\Models\LocalFundraising;
use App\Models\MissionAverage;
use App\Models\MissionMetric;
use App\Models\MissionMonthlyData;
use App\Models\PnlLineItem;
use App\Models\PnlValue;
use App\Models\RevenueSharing;
use App\Models\RevenueSource;
use App\Models\DesignSetting;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Month names indexed 1-12 for converting numeric months to display names.
     */
    private const MONTH_NAMES = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    /**
     * Numeric fields on MissionMetric that should be summed for "All Areas".
     */
    private const MISSION_SUM_FIELDS = [
        'families_served', 'individuals_served', 'hosted_days', 'hosted_nights',
        'total_hosted', 'total_volunteers', 'partner_churches', 'service_hours',
        'intake', 'opened', 'graduations', 'total_relationships',
    ];

    /**
     * Fields on MissionAverage that are summed (totals + dec snapshots) for "All Areas".
     */
    private const MISSION_V2_SUM_FIELDS = [
        'unique_families', 'unique_individuals', 'total_intake', 'total_opened',
        'total_matched', 'total_graduations', 'total_hosted_days', 'total_hosted_nights',
        'total_hosted', 'total_service_hours',
        'dec_families', 'dec_individuals', 'dec_volunteers', 'dec_active_volunteers',
        'dec_partner_churches', 'dec_hosting', 'dec_friendships', 'dec_coaching',
        'dec_relationships',
    ];

    /**
     * Fields on MissionAverage that are averaged across areas for "All Areas".
     */
    private const MISSION_V2_AVG_FIELDS = [
        'avg_families', 'avg_individuals', 'avg_volunteers', 'avg_active_volunteers',
        'avg_partner_churches', 'avg_hosting', 'avg_friendships', 'avg_coaching',
        'avg_relationships',
    ];

    /**
     * Monthly data fields that should be summed for "All Areas".
     */
    private const MONTHLY_SUM_FIELDS = [
        'families', 'individuals', 'matched', 'intake', 'hosted_days', 'hosted_nights',
        'volunteers', 'active_volunteers', 'partner_churches', 'active_hosting',
        'active_friendships', 'active_coaching', 'graduations',
    ];

    /**
     * Budget fields used in the budget_2026 structure.
     */
    private const BUDGET_FIELDS = [
        'revenue', 'cogs', 'gross_profit', 'program_costs', 'admin_costs',
        'total_expenses', 'net_operating', 'rev_sharing', 'net_revenue',
        'individual_donations', 'church_giving', 'grant_revenue',
        'foundation_revenue', 'fundraising_events', 'institutional',
    ];

    /**
     * Expense summary fields.
     */
    private const EXPENSE_FIELDS = ['program', 'admin', 'fundraising', 'total'];

    /**
     * Efficiency metric fields.
     */
    private const EFFICIENCY_FIELDS = [
        'cost_per_individual', 'cost_per_family', 'cost_per_hosted', 'cost_per_intake',
        'cost_per_graduation', 'cost_per_service_hour', 'program_cost_ratio', 'admin_ratio',
        'fundraising_roi', 'rev_per_volunteer', 'ind_per_10k_staff', 'intake_conversion',
        'program_cost', 'revenue',
    ];

    /**
     * Display the main dashboard.
     */
    public function index()
    {
        // Load all areas ordered by sort_order
        $areas = Area::ordered()->get();
        $areaNames = $areas->pluck('name')->toArray();
        $areaMap = $areas->pluck('name', 'id');  // id => name

        // Build the D object matching the original JS structure
        $D = [
            'areas'             => array_merge(['All Areas'], $areaNames),
            'field_areas'       => $areaNames,
            'pnl_by_area'       => $this->buildPnlByArea($areas, $areaMap),
            'rev_by_area'       => $this->buildRevByArea($areas, $areaMap),
            'exp_by_area'       => $this->buildExpByArea($areas, $areaMap),
            'rev_sources'       => $this->buildRevSources($areas, $areaMap),
            'mission'           => $this->buildMission($areas, $areaMap),
            'mission_v2'        => $this->buildMissionV2($areas, $areaMap),
            'efficiency'        => $this->buildEfficiency($areas, $areaMap),
            'budget_2026'       => $this->buildBudget2026($areas, $areaMap),
            'equity'            => $this->buildFinancialField($areas, $areaMap, 'equity', 2025),
            'net_assets'        => $this->buildFinancialField($areas, $areaMap, 'net_assets', 2025),
            'net_income_2025_bs'=> $this->buildFinancialField($areas, $areaMap, 'net_income_bs', 2025),
            'staffing_2026'     => $this->buildFinancialField($areas, $areaMap, 'staffing_budget', 2026),
            'target_reserve'    => $this->buildFinancialField($areas, $areaMap, 'target_reserve', 2025),
            'rev_sharing'       => $this->buildSimpleYearAreaAmount(RevenueSharing::class, $areas, $areaMap),
            'local_fundraising' => $this->buildSimpleYearAreaAmount(LocalFundraising::class, $areas, $areaMap),
            'highlight_groups'  => $this->buildHighlightGroups(),

            // GL drill-down support
            'gl_available_years' => GlTransaction::distinct()->pluck('fiscal_year')->sort()->values()->toArray(),
            'pnl_line_item_ids' => PnlLineItem::ordered()->pluck('id')->toArray(),
            'area_ids' => $areas->pluck('id', 'name')->toArray(),
        ];

        $active = DesignSetting::active();

        return view('dashboard', [
            'dashboardData'      => $D,
            'designSettings'     => $active->settings,
            'darkDesignSettings' => $active->dark_settings,
        ]);
    }

    // ── P&L By Area ─────────────────────────────────────────────────

    /**
     * Build pnl_by_area: { "All Areas": [{label, isTotal, "2023": val, ...}], "Chippewa Valley": [...], ... }
     */
    private function buildPnlByArea(Collection $areas, Collection $areaMap): array
    {
        $lineItems = PnlLineItem::ordered()->get();
        $pnlYears = PnlValue::distinct()->pluck('fiscal_year')->sort()->values()->toArray();

        // Load all PnL values keyed for fast lookup: [area_id][line_item_id][year] => amount
        $allValues = PnlValue::all()->groupBy('area_id');
        $lookup = [];
        foreach ($allValues as $areaId => $values) {
            foreach ($values as $v) {
                $lookup[$areaId][$v->line_item_id][$v->fiscal_year] = (float) $v->amount;
            }
        }

        $result = [];

        // Build per-area arrays
        foreach ($areas as $area) {
            $areaRows = [];
            foreach ($lineItems as $li) {
                $row = [
                    'label'   => $li->label,
                    'isTotal' => $li->is_total,
                ];
                foreach ($pnlYears as $year) {
                    $row[(string) $year] = $lookup[$area->id][$li->id][$year] ?? 0;
                }
                $areaRows[] = $row;
            }
            $result[$area->name] = $areaRows;
        }

        // Compute "All Areas" by summing across all areas for each line item/year
        $allAreasRows = [];
        foreach ($lineItems as $li) {
            $row = [
                'label'   => $li->label,
                'isTotal' => $li->is_total,
            ];
            foreach ($pnlYears as $year) {
                $sum = 0;
                foreach ($areas as $area) {
                    $sum += $lookup[$area->id][$li->id][$year] ?? 0;
                }
                $row[(string) $year] = $sum;
            }
            $allAreasRows[] = $row;
        }

        // Put "All Areas" first
        return array_merge(['All Areas' => $allAreasRows], $result);
    }

    // ── Revenue By Area ─────────────────────────────────────────────

    /**
     * Build rev_by_area: { "2023": {"All Areas": total, "Chippewa Valley": total, ...}, ... }
     * This is the TOTAL INCOME (sort_order=8) per area per year.
     */
    private function buildRevByArea(Collection $areas, Collection $areaMap): array
    {
        // Find the TOTAL INCOME line item (sort_order = 8)
        $totalIncomeLine = PnlLineItem::where('sort_order', 8)->first();
        if (! $totalIncomeLine) {
            return [];
        }

        $pnlYears = PnlValue::distinct()->pluck('fiscal_year')->sort()->values()->toArray();
        $values = PnlValue::where('line_item_id', $totalIncomeLine->id)->get();

        // Index: [year][area_id] => amount
        $lookup = [];
        foreach ($values as $v) {
            $lookup[$v->fiscal_year][$v->area_id] = (float) $v->amount;
        }

        $result = [];
        foreach ($pnlYears as $year) {
            $yearData = [];
            $allAreasSum = 0;

            foreach ($areas as $area) {
                $val = $lookup[$year][$area->id] ?? 0;
                $yearData[$area->name] = $val;
                $allAreasSum += $val;
            }

            $result[(string) $year] = array_merge(['All Areas' => $allAreasSum], $yearData);
        }

        return $result;
    }

    // ── Expenses By Area ────────────────────────────────────────────

    /**
     * Build exp_by_area: { "2024": {"All Areas": {program, admin, fundraising, total}, ...}, ... }
     */
    private function buildExpByArea(Collection $areas, Collection $areaMap): array
    {
        $records = ExpenseSummary::all();
        $years = $records->pluck('fiscal_year')->unique()->sort()->values();

        // Index: [year][area_id] => record
        $lookup = [];
        foreach ($records as $r) {
            $lookup[$r->fiscal_year][$r->area_id] = $r;
        }

        $result = [];
        foreach ($years as $year) {
            $yearData = [];
            $allAreasSums = array_fill_keys(self::EXPENSE_FIELDS, 0);

            foreach ($areas as $area) {
                if (isset($lookup[$year][$area->id])) {
                    $r = $lookup[$year][$area->id];
                    $areaData = [];
                    foreach (self::EXPENSE_FIELDS as $field) {
                        $val = (float) ($r->$field ?? 0);
                        $areaData[$field] = $val;
                        $allAreasSums[$field] += $val;
                    }
                    $yearData[$area->name] = $areaData;
                }
            }

            $result[(string) $year] = array_merge(['All Areas' => $allAreasSums], $yearData);
        }

        return $result;
    }

    // ── Revenue Sources ─────────────────────────────────────────────

    /**
     * Build rev_sources: { "2023": {"All Areas": [{label, value}, ...], ...}, ... }
     * Only includes sources with non-zero amounts.
     */
    private function buildRevSources(Collection $areas, Collection $areaMap): array
    {
        $records = RevenueSource::orderBy('sort_order')->get();
        $years = $records->pluck('fiscal_year')->unique()->sort()->values();

        // Group: [year][area_id] => collection of records
        $grouped = [];
        foreach ($records as $r) {
            $grouped[$r->fiscal_year][$r->area_id][] = $r;
        }

        $result = [];
        foreach ($years as $year) {
            $yearData = [];

            // Aggregate for "All Areas": sum by label across all areas
            $allAreasByLabel = [];

            foreach ($areas as $area) {
                if (isset($grouped[$year][$area->id])) {
                    $areaSources = [];
                    foreach ($grouped[$year][$area->id] as $r) {
                        $val = (float) ($r->amount ?? 0);
                        if ($val != 0) {
                            $areaSources[] = ['label' => $r->label, 'value' => $val];
                        }
                        // Accumulate for All Areas
                        if (! isset($allAreasByLabel[$r->label])) {
                            $allAreasByLabel[$r->label] = 0;
                        }
                        $allAreasByLabel[$r->label] += $val;
                    }
                    $yearData[$area->name] = $areaSources;
                }
            }

            // Build All Areas array (only non-zero values)
            $allAreasArr = [];
            foreach ($allAreasByLabel as $label => $val) {
                if ($val != 0) {
                    $allAreasArr[] = ['label' => $label, 'value' => $val];
                }
            }

            $result[(string) $year] = array_merge(['All Areas' => $allAreasArr], $yearData);
        }

        return $result;
    }

    // ── Mission Metrics ─────────────────────────────────────────────

    /**
     * Build mission: { "2024": {"All Areas": {individuals_served, ...}, "Chippewa Valley": {...}, ...}, ... }
     */
    private function buildMission(Collection $areas, Collection $areaMap): array
    {
        $records = MissionMetric::all();
        $years = $records->pluck('fiscal_year')->unique()->sort()->values();

        // Index: [year][area_id] => record
        $lookup = [];
        foreach ($records as $r) {
            $lookup[$r->fiscal_year][$r->area_id] = $r;
        }

        $result = [];
        foreach ($years as $year) {
            $yearData = [];
            $allAreasSums = array_fill_keys(self::MISSION_SUM_FIELDS, 0);
            $allAreasAvgMonthlyFamilies = 0;

            foreach ($areas as $area) {
                if (isset($lookup[$year][$area->id])) {
                    $r = $lookup[$year][$area->id];
                    $areaData = [];
                    foreach (self::MISSION_SUM_FIELDS as $field) {
                        $val = (int) ($r->$field ?? 0);
                        $areaData[$field] = $val;
                        $allAreasSums[$field] += $val;
                    }
                    // avg_monthly_families
                    $areaData['avg_monthly_families'] = (float) ($r->avg_monthly_families ?? 0);
                    $allAreasAvgMonthlyFamilies += $areaData['avg_monthly_families'];

                    $yearData[$area->name] = $areaData;
                }
            }

            // Build the "All Areas" mission data with fields in the correct order
            $allAreas = [
                'individuals_served'    => $allAreasSums['individuals_served'],
                'families_served'       => $allAreasSums['families_served'],
                'avg_monthly_families'  => round($allAreasAvgMonthlyFamilies, 1),
                'hosted_days'           => $allAreasSums['hosted_days'],
                'hosted_nights'         => $allAreasSums['hosted_nights'],
                'total_hosted'          => $allAreasSums['total_hosted'],
                'total_volunteers'      => $allAreasSums['total_volunteers'],
                'partner_churches'      => $allAreasSums['partner_churches'],
                'service_hours'         => $allAreasSums['service_hours'],
                'intake'                => $allAreasSums['intake'],
                'opened'                => $allAreasSums['opened'],
                'graduations'           => $allAreasSums['graduations'],
                'total_relationships'   => $allAreasSums['total_relationships'],
            ];

            $result[(string) $year] = array_merge(['All Areas' => $allAreas], $yearData);
        }

        return $result;
    }

    // ── Mission V2 (Averages + Monthly Data) ────────────────────────

    /**
     * Build mission_v2: { "2024": {"Kenosha/Racine": {avg_*, total_*, dec_*, ind_fam_ratio, monthly_data: [...]}, ...}, ... }
     */
    private function buildMissionV2(Collection $areas, Collection $areaMap): array
    {
        $averages = MissionAverage::all();
        $monthlyData = MissionMonthlyData::orderBy('month')->get();

        $avgYears = $averages->pluck('fiscal_year')->unique()->sort()->values();

        // Index averages: [year][area_id] => record
        $avgLookup = [];
        foreach ($averages as $a) {
            $avgLookup[$a->fiscal_year][$a->area_id] = $a;
        }

        // Index monthly data: [year][area_id] => collection of records ordered by month
        $monthlyLookup = [];
        foreach ($monthlyData as $m) {
            $monthlyLookup[$m->fiscal_year][$m->area_id][] = $m;
        }

        $allSumFields = array_merge(self::MISSION_V2_SUM_FIELDS);
        $allAvgFields = array_merge(self::MISSION_V2_AVG_FIELDS);

        $result = [];
        foreach ($avgYears as $year) {
            $yearData = [];
            $allAreasSums = array_fill_keys($allSumFields, 0);
            $allAreasAvgSums = array_fill_keys($allAvgFields, 0);
            $areaCount = 0;

            // Track monthly sums for "All Areas"
            $allAreasMonthly = [];

            foreach ($areas as $area) {
                if (! isset($avgLookup[$year][$area->id])) {
                    continue;
                }

                $areaCount++;
                $avg = $avgLookup[$year][$area->id];

                $areaData = [];

                // Avg fields
                foreach ($allAvgFields as $field) {
                    $val = (float) ($avg->$field ?? 0);
                    $areaData[$field] = $val;
                    $allAreasAvgSums[$field] += $val;
                }

                // Sum/total fields
                foreach ($allSumFields as $field) {
                    $val = $avg->$field;
                    $areaData[$field] = $val !== null ? (int) $val : null;
                    $allAreasSums[$field] += (int) ($val ?? 0);
                }

                // ind_fam_ratio
                $areaData['ind_fam_ratio'] = (float) ($avg->ind_fam_ratio ?? 0);

                // Monthly data
                if (isset($monthlyLookup[$year][$area->id])) {
                    $monthly = [];
                    foreach ($monthlyLookup[$year][$area->id] as $m) {
                        $monthRow = ['month' => self::MONTH_NAMES[$m->month] ?? 'Unknown'];
                        foreach (self::MONTHLY_SUM_FIELDS as $field) {
                            $val = $m->$field;
                            $monthRow[$field] = $val !== null ? (float) $val : 0;

                            // Accumulate for All Areas monthly
                            if (! isset($allAreasMonthly[$m->month])) {
                                $allAreasMonthly[$m->month] = array_fill_keys(self::MONTHLY_SUM_FIELDS, 0);
                            }
                            $allAreasMonthly[$m->month][$field] += (float) ($val ?? 0);
                        }
                        $monthly[] = $monthRow;
                    }
                    $areaData['monthly_data'] = $monthly;
                } else {
                    $areaData['monthly_data'] = [];
                }

                $yearData[$area->name] = $areaData;
            }

            // Build "All Areas" aggregate
            if ($areaCount > 0) {
                $allAreasData = [];

                // Averages: sum of per-area averages (matching the original D object behavior)
                foreach ($allAvgFields as $field) {
                    $allAreasData[$field] = round($allAreasAvgSums[$field], 1);
                }

                // Sums
                foreach ($allSumFields as $field) {
                    $allAreasData[$field] = $allAreasSums[$field];
                }

                // ind_fam_ratio: compute from aggregated dec values
                $decFam = $allAreasSums['dec_families'] ?? 0;
                $decInd = $allAreasSums['dec_individuals'] ?? 0;
                $allAreasData['ind_fam_ratio'] = $decFam > 0 ? round($decInd / $decFam, 1) : 0;

                // Monthly data for All Areas
                if (! empty($allAreasMonthly)) {
                    $allMonthly = [];
                    foreach (range(1, 12) as $monthNum) {
                        if (isset($allAreasMonthly[$monthNum])) {
                            $monthRow = ['month' => self::MONTH_NAMES[$monthNum]];
                            foreach (self::MONTHLY_SUM_FIELDS as $field) {
                                $monthRow[$field] = $allAreasMonthly[$monthNum][$field];
                            }
                            $allMonthly[] = $monthRow;
                        }
                    }
                    $allAreasData['monthly_data'] = $allMonthly;
                } else {
                    $allAreasData['monthly_data'] = [];
                }

                $yearData['All Areas'] = $allAreasData;
            }

            $result[(string) $year] = $yearData;
        }

        return $result;
    }

    // ── Efficiency Metrics ──────────────────────────────────────────

    /**
     * Build efficiency: { "2024": {"All Areas": {cost_per_individual, ...}, ...}, ... }
     * "All Areas" comes directly from the database (pre-computed during seeding).
     */
    private function buildEfficiency(Collection $areas, Collection $areaMap): array
    {
        $records = EfficiencyMetric::all();
        $years = $records->pluck('fiscal_year')->unique()->sort()->values();

        // Index: [year][area_id] => record
        $lookup = [];
        foreach ($records as $r) {
            $lookup[$r->fiscal_year][$r->area_id] = $r;
        }

        $result = [];
        foreach ($years as $year) {
            $yearData = [];

            // Compute "All Areas" by summing across all areas
            $allAreasSums = array_fill_keys(self::EFFICIENCY_FIELDS, 0);
            $allAreasCount = 0;

            foreach ($areas as $area) {
                if (isset($lookup[$year][$area->id])) {
                    $r = $lookup[$year][$area->id];
                    $areaData = [];
                    foreach (self::EFFICIENCY_FIELDS as $field) {
                        $val = $r->$field !== null ? (float) $r->$field : null;
                        $areaData[$field] = $val;
                    }
                    $yearData[$area->name] = $areaData;

                    // Accumulate for "All Areas" aggregate
                    foreach (self::EFFICIENCY_FIELDS as $field) {
                        $allAreasSums[$field] += (float) ($r->$field ?? 0);
                    }
                    $allAreasCount++;
                }
            }

            // "All Areas" efficiency: use summed program_cost and revenue,
            // then recompute ratio metrics from the summed values
            $allAreas = [];
            $totalProgramCost = $allAreasSums['program_cost'];
            $totalRevenue = $allAreasSums['revenue'];

            // For per-unit cost metrics, we need mission data to compute them properly.
            // The original data had pre-computed "All Areas" values.
            // Since efficiency records are stored per-area (including from the original "All Areas"),
            // we sum the numeric values.
            foreach (self::EFFICIENCY_FIELDS as $field) {
                $allAreas[$field] = $allAreasSums[$field];
            }

            // For ratio fields, recompute from summed totals rather than summing ratios
            if ($totalRevenue > 0) {
                $allAreas['program_cost_ratio'] = round($totalProgramCost / $totalRevenue, 4);
                $allAreas['admin_ratio'] = round($allAreasSums['admin_ratio'] / max($allAreasCount, 1), 4);
            }

            $result[(string) $year] = array_merge(['All Areas' => $allAreas], $yearData);
        }

        return $result;
    }

    // ── Budget 2026 ─────────────────────────────────────────────────

    /**
     * Build budget_2026: { "Chippewa Valley": {revenue, cogs, ...}, ... }
     */
    private function buildBudget2026(Collection $areas, Collection $areaMap): array
    {
        $budgets = Budget::where('fiscal_year', 2026)->get();

        // Index: [area_id] => record
        $lookup = [];
        foreach ($budgets as $b) {
            $lookup[$b->area_id] = $b;
        }

        $result = [];
        $allAreasSums = array_fill_keys(self::BUDGET_FIELDS, 0);

        foreach ($areas as $area) {
            if (isset($lookup[$area->id])) {
                $b = $lookup[$area->id];
                $areaData = [];
                foreach (self::BUDGET_FIELDS as $field) {
                    $val = (float) ($b->$field ?? 0);
                    $areaData[$field] = $val;
                    $allAreasSums[$field] += $val;
                }
                $result[$area->name] = $areaData;
            }
        }

        // "All Areas" as sum
        return array_merge(['All Areas' => $allAreasSums], $result);
    }

    // ── Financial Snapshots (single-value maps) ─────────────────────

    /**
     * Build a simple area => value map for a single financial snapshot field/year.
     * Used for equity, net_assets, net_income_2025_bs, staffing_2026, target_reserve.
     */
    private function buildFinancialField(Collection $areas, Collection $areaMap, string $field, int $year): array
    {
        $snapshots = FinancialSnapshot::where('fiscal_year', $year)->get();

        $result = [];
        $allAreasSum = 0;

        foreach ($areas as $area) {
            $snap = $snapshots->firstWhere('area_id', $area->id);
            $val = $snap ? (float) ($snap->$field ?? 0) : 0;
            $result[$area->name] = $val;
            $allAreasSum += $val;
        }

        return array_merge(['All Areas' => $allAreasSum], $result);
    }

    // ── Simple Year -> Area -> Amount Tables ────────────────────────

    /**
     * Build a year -> area -> amount structure.
     * Used for rev_sharing and local_fundraising.
     *
     * Result: { "2023": {"All Areas": sum, "Chippewa Valley": val, ...}, ... }
     */
    private function buildSimpleYearAreaAmount(string $modelClass, Collection $areas, Collection $areaMap): array
    {
        $records = $modelClass::all();
        $years = $records->pluck('fiscal_year')->unique()->sort()->values();

        // Index: [year][area_id] => amount
        $lookup = [];
        foreach ($records as $r) {
            $lookup[$r->fiscal_year][$r->area_id] = (float) ($r->amount ?? 0);
        }

        $result = [];
        foreach ($years as $year) {
            $yearData = [];
            $allAreasSum = 0;

            foreach ($areas as $area) {
                $val = $lookup[$year][$area->id] ?? 0;
                $yearData[$area->name] = $val;
                $allAreasSum += $val;
            }

            $result[(string) $year] = array_merge(['All Areas' => $allAreasSum], $yearData);
        }

        return $result;
    }

    // ── Highlight Groups ────────────────────────────────────────────

    private function buildHighlightGroups(): array
    {
        return HighlightGroup::orderBy('sort_order')
            ->with(['kpis' => fn ($q) => $q->orderByPivot('sort_order')])
            ->get()
            ->map(fn ($g) => [
                'title' => $g->title,
                'sub'   => $g->subtitle,
                'color' => $g->color,
                'kpis'  => $g->kpis->map(fn ($k) => [
                    'l'    => $k->label,
                    'k'    => $k->key,
                    'type' => $k->type,
                    'dec'  => $k->is_decimal,
                    'bc'   => $k->color_class,
                ])->toArray(),
            ])->toArray();
    }
}
