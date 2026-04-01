<?php

namespace Database\Seeders;

use App\Models\HighlightGroup;
use App\Models\HighlightKpi;
use Illuminate\Database\Seeder;

class HighlightSeeder extends Seeder
{
    public function run(): void
    {
        // ── KPI Pool ────────────────────────────────────────────────
        $kpis = [
            ['label' => 'Avg Families / Mo',       'key' => 'avg_families',       'type' => 'mission', 'is_decimal' => true,  'color_class' => 'green', 'sort_order' => 1],
            ['label' => 'Avg Individuals / Mo',     'key' => 'avg_individuals',    'type' => 'mission', 'is_decimal' => true,  'color_class' => 'green', 'sort_order' => 2],
            ['label' => 'Monthly Cost / Family',    'key' => 'fam',                'type' => 'cost',    'is_decimal' => false, 'color_class' => 'accent', 'sort_order' => 3],
            ['label' => 'Monthly Cost / Individual','key' => 'ind',                'type' => 'cost',    'is_decimal' => false, 'color_class' => 'accent', 'sort_order' => 4],
            ['label' => 'Net Fundraising',          'key' => 'direct_fundraising', 'type' => 'fin',     'is_decimal' => false, 'color_class' => 'warm',  'sort_order' => 5],
            ['label' => 'Operating Expenses',       'key' => 'opex',               'type' => 'fin',     'is_decimal' => false, 'color_class' => 'warm',  'sort_order' => 6],
        ];

        $kpiModels = [];
        foreach ($kpis as $kpi) {
            $kpiModels[$kpi['key']] = HighlightKpi::create($kpi);
        }

        // ── Groups ──────────────────────────────────────────────────
        $group1 = HighlightGroup::create([
            'title'      => "We're serving more families.",
            'subtitle'   => 'Average monthly caseload grew year-over-year.',
            'color'      => 'green',
            'sort_order' => 1,
        ]);
        $group1->kpis()->attach([
            $kpiModels['avg_families']->id    => ['sort_order' => 1],
            $kpiModels['avg_individuals']->id => ['sort_order' => 2],
        ]);

        $group2 = HighlightGroup::create([
            'title'      => "We're doing it more efficiently.",
            'subtitle'   => 'Cost per family and individual both decreased.',
            'color'      => 'accent',
            'sort_order' => 2,
        ]);
        $group2->kpis()->attach([
            $kpiModels['fam']->id => ['sort_order' => 1],
            $kpiModels['ind']->id => ['sort_order' => 2],
        ]);

        $group3 = HighlightGroup::create([
            'title'      => 'Revenue outpaced expense growth.',
            'subtitle'   => 'Net fundraising grew vs expense growth.',
            'color'      => 'warm',
            'sort_order' => 3,
        ]);
        $group3->kpis()->attach([
            $kpiModels['direct_fundraising']->id => ['sort_order' => 1],
            $kpiModels['opex']->id               => ['sort_order' => 2],
        ]);
    }
}
