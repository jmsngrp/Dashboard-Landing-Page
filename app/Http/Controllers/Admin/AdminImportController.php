<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Budget;
use App\Models\EfficiencyMetric;
use App\Models\ExpenseSummary;
use App\Models\FinancialSnapshot;
use App\Models\LocalFundraising;
use App\Models\MissionMetric;
use App\Models\PnlLineItem;
use App\Models\PnlValue;
use App\Models\RevenueSharing;
use App\Models\RevenueSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminImportController extends Controller
{
    /**
     * The available import targets and their configurations.
     */
    private const IMPORT_TARGETS = [
        'pnl_values'          => 'P&L Values',
        'mission_metrics'     => 'Mission Metrics',
        'efficiency_metrics'  => 'Efficiency Metrics',
        'revenue_sources'     => 'Revenue Sources',
        'expense_summaries'   => 'Expense Summaries',
        'budgets'             => 'Budgets',
        'financial_snapshots' => 'Financial Snapshots',
        'revenue_sharing'     => 'Revenue Sharing',
        'local_fundraising'   => 'Local Fundraising',
    ];

    public function index()
    {
        $targets = self::IMPORT_TARGETS;
        return view('admin.import.index', compact('targets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'target' => 'required|string|in:' . implode(',', array_keys(self::IMPORT_TARGETS)),
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $target = $request->input('target');
        $file = $request->file('csv_file');

        try {
            $handle = fopen($file->getPathname(), 'r');
            $header = fgetcsv($handle);

            if (!$header) {
                fclose($handle);
                return redirect()->route('admin.import.index')
                    ->with('error', 'CSV file is empty or could not be read.');
            }

            // Clean BOM from header
            $header = array_map(function ($col) {
                return trim(preg_replace('/[\x{FEFF}]/u', '', $col));
            }, $header);

            $rowCount = 0;

            DB::beginTransaction();

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== count($header)) {
                    continue;
                }

                $data = array_combine($header, $row);
                $this->importRow($target, $data);
                $rowCount++;
            }

            DB::commit();
            fclose($handle);

            return redirect()->route('admin.import.index')
                ->with('success', "Successfully imported {$rowCount} rows into " . self::IMPORT_TARGETS[$target] . '.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.import.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function importRow(string $target, array $data): void
    {
        // Resolve area by slug or name
        $areaId = null;
        if (isset($data['area_slug'])) {
            $area = Area::where('slug', $data['area_slug'])->first();
            $areaId = $area?->id;
        } elseif (isset($data['area_name'])) {
            $area = Area::where('name', $data['area_name'])->first();
            $areaId = $area?->id;
        } elseif (isset($data['area_id'])) {
            $areaId = (int) $data['area_id'];
        }

        if (!$areaId) {
            return;
        }

        $year = (int) ($data['fiscal_year'] ?? 0);

        switch ($target) {
            case 'pnl_values':
                $lineItem = PnlLineItem::where('label', $data['line_item'] ?? '')->first();
                if ($lineItem) {
                    PnlValue::updateOrCreate(
                        ['area_id' => $areaId, 'line_item_id' => $lineItem->id, 'fiscal_year' => $year],
                        ['amount' => (float) ($data['amount'] ?? 0)]
                    );
                }
                break;

            case 'mission_metrics':
                MissionMetric::updateOrCreate(
                    ['area_id' => $areaId, 'fiscal_year' => $year],
                    array_intersect_key($data, array_flip((new MissionMetric)->getFillable()))
                );
                break;

            case 'efficiency_metrics':
                EfficiencyMetric::updateOrCreate(
                    ['area_id' => $areaId, 'fiscal_year' => $year],
                    array_intersect_key($data, array_flip((new EfficiencyMetric)->getFillable()))
                );
                break;

            case 'revenue_sources':
                RevenueSource::create(array_merge(
                    ['area_id' => $areaId, 'fiscal_year' => $year],
                    array_intersect_key($data, array_flip((new RevenueSource)->getFillable()))
                ));
                break;

            case 'expense_summaries':
                ExpenseSummary::updateOrCreate(
                    ['area_id' => $areaId, 'fiscal_year' => $year],
                    array_intersect_key($data, array_flip((new ExpenseSummary)->getFillable()))
                );
                break;

            case 'budgets':
                Budget::updateOrCreate(
                    ['area_id' => $areaId, 'fiscal_year' => $year],
                    array_intersect_key($data, array_flip((new Budget)->getFillable()))
                );
                break;

            case 'financial_snapshots':
                FinancialSnapshot::updateOrCreate(
                    ['area_id' => $areaId, 'fiscal_year' => $year],
                    array_intersect_key($data, array_flip((new FinancialSnapshot)->getFillable()))
                );
                break;

            case 'revenue_sharing':
                RevenueSharing::updateOrCreate(
                    ['area_id' => $areaId, 'fiscal_year' => $year],
                    ['amount' => (float) ($data['amount'] ?? 0)]
                );
                break;

            case 'local_fundraising':
                LocalFundraising::updateOrCreate(
                    ['area_id' => $areaId, 'fiscal_year' => $year],
                    ['amount' => (float) ($data['amount'] ?? 0)]
                );
                break;
        }
    }
}
