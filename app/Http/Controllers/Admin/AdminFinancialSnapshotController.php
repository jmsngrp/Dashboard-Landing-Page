<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\FinancialSnapshot;
use Illuminate\Http\Request;

class AdminFinancialSnapshotController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::ordered()->get();
        $years = FinancialSnapshot::distinct()->pluck('fiscal_year')->sort()->values();

        $query = FinancialSnapshot::with('area');

        if ($request->filled('year')) {
            $query->where('fiscal_year', $request->year);
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $records = $query->orderBy('fiscal_year', 'desc')->orderBy('area_id')->get();

        return view('admin.financial-snapshots.index', compact('areas', 'years', 'records'));
    }

    public function create()
    {
        $areas = Area::ordered()->get();
        return view('admin.financial-snapshots.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id'         => 'required|exists:areas,id',
            'fiscal_year'     => 'required|integer|min:2000|max:2100',
            'equity'          => 'nullable|numeric',
            'net_assets'      => 'nullable|numeric',
            'net_income_bs'   => 'nullable|numeric',
            'staffing_budget' => 'nullable|numeric',
            'target_reserve'  => 'nullable|numeric',
        ]);

        FinancialSnapshot::create($validated);

        return redirect()->route('admin.financial-snapshots.index')
            ->with('success', 'Financial snapshot created successfully.');
    }

    public function edit(FinancialSnapshot $financial_snapshot)
    {
        $areas = Area::ordered()->get();
        return view('admin.financial-snapshots.edit', compact('financial_snapshot', 'areas'));
    }

    public function update(Request $request, FinancialSnapshot $financial_snapshot)
    {
        $validated = $request->validate([
            'area_id'         => 'required|exists:areas,id',
            'fiscal_year'     => 'required|integer|min:2000|max:2100',
            'equity'          => 'nullable|numeric',
            'net_assets'      => 'nullable|numeric',
            'net_income_bs'   => 'nullable|numeric',
            'staffing_budget' => 'nullable|numeric',
            'target_reserve'  => 'nullable|numeric',
        ]);

        $financial_snapshot->update($validated);

        return redirect()->route('admin.financial-snapshots.index')
            ->with('success', 'Financial snapshot updated successfully.');
    }

    public function destroy(FinancialSnapshot $financial_snapshot)
    {
        $financial_snapshot->delete();

        return redirect()->route('admin.financial-snapshots.index')
            ->with('success', 'Financial snapshot deleted successfully.');
    }
}
