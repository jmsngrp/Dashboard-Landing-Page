<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\BudgetBucket;
use App\Models\BudgetBucketAmount;
use App\Services\FinancialComputeService;
use Illuminate\Http\Request;

class AdminBucketAmountController extends Controller
{
    public function index(Request $request, FinancialComputeService $finance)
    {
        $areas = Area::ordered()->get();
        $buckets = BudgetBucket::active()->ordered()->get();
        $years = $finance->getAvailableYears();

        if (empty($years)) {
            $years = [(int) date('Y')];
        }

        $selectedYear = (int) $request->get('year', end($years));

        // Load amounts for the selected year: [bucket_id][area_id] => amount record
        $amounts = BudgetBucketAmount::where('fiscal_year', $selectedYear)
            ->get()
            ->groupBy('budget_bucket_id');

        $lookup = [];
        foreach ($amounts as $bucketId => $records) {
            foreach ($records as $record) {
                $lookup[$bucketId][$record->area_id] = $record;
            }
        }

        return view('admin.bucket-amounts.index', compact(
            'areas',
            'buckets',
            'years',
            'selectedYear',
            'lookup'
        ));
    }

    public function update(Request $request)
    {
        $year = (int) $request->input('fiscal_year');
        $data = $request->input('amounts', []);

        foreach ($data as $bucketId => $areas) {
            foreach ($areas as $areaId => $values) {
                $budgetAmount = $values['budget'] ?? null;
                $manualActual = $values['actual'] ?? null;

                // Skip if both are empty
                if ($budgetAmount === null && $manualActual === null &&
                    $budgetAmount === '' && $manualActual === '') {
                    continue;
                }

                BudgetBucketAmount::updateOrCreate(
                    [
                        'budget_bucket_id' => $bucketId,
                        'area_id'          => $areaId,
                        'fiscal_year'      => $year,
                    ],
                    [
                        'budget_amount' => $budgetAmount !== '' && $budgetAmount !== null
                            ? (float) $budgetAmount : null,
                        'manual_actual' => $manualActual !== '' && $manualActual !== null
                            ? (float) $manualActual : null,
                    ]
                );
            }
        }

        return redirect()->route('admin.bucket-amounts.index', ['year' => $year])
            ->with('success', 'Bucket amounts saved.');
    }
}
