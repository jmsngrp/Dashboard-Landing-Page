<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\EfficiencyMetric;
use Illuminate\Http\Request;

class AdminEfficiencyController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::ordered()->get();
        $years = EfficiencyMetric::distinct()->pluck('fiscal_year')->sort()->values();

        $query = EfficiencyMetric::with('area');

        if ($request->filled('year')) {
            $query->where('fiscal_year', $request->year);
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $records = $query->orderBy('fiscal_year', 'desc')->orderBy('area_id')->get();

        return view('admin.efficiency.index', compact('areas', 'years', 'records'));
    }

    public function create()
    {
        $areas = Area::ordered()->get();
        return view('admin.efficiency.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id'               => 'required|exists:areas,id',
            'fiscal_year'           => 'required|integer|min:2000|max:2100',
            'cost_per_individual'   => 'nullable|numeric',
            'cost_per_family'       => 'nullable|numeric',
            'cost_per_hosted'       => 'nullable|numeric',
            'cost_per_intake'       => 'nullable|numeric',
            'cost_per_graduation'   => 'nullable|numeric',
            'cost_per_service_hour' => 'nullable|numeric',
            'program_cost_ratio'    => 'nullable|numeric',
            'admin_ratio'           => 'nullable|numeric',
            'fundraising_roi'       => 'nullable|numeric',
            'rev_per_volunteer'     => 'nullable|numeric',
            'ind_per_10k_staff'     => 'nullable|numeric',
            'intake_conversion'     => 'nullable|numeric',
            'program_cost'          => 'nullable|numeric',
            'revenue'               => 'nullable|numeric',
        ]);

        EfficiencyMetric::create($validated);

        return redirect()->route('admin.efficiency.index')
            ->with('success', 'Efficiency metric record created successfully.');
    }

    public function edit(EfficiencyMetric $efficiency)
    {
        $areas = Area::ordered()->get();
        return view('admin.efficiency.edit', compact('efficiency', 'areas'));
    }

    public function update(Request $request, EfficiencyMetric $efficiency)
    {
        $validated = $request->validate([
            'area_id'               => 'required|exists:areas,id',
            'fiscal_year'           => 'required|integer|min:2000|max:2100',
            'cost_per_individual'   => 'nullable|numeric',
            'cost_per_family'       => 'nullable|numeric',
            'cost_per_hosted'       => 'nullable|numeric',
            'cost_per_intake'       => 'nullable|numeric',
            'cost_per_graduation'   => 'nullable|numeric',
            'cost_per_service_hour' => 'nullable|numeric',
            'program_cost_ratio'    => 'nullable|numeric',
            'admin_ratio'           => 'nullable|numeric',
            'fundraising_roi'       => 'nullable|numeric',
            'rev_per_volunteer'     => 'nullable|numeric',
            'ind_per_10k_staff'     => 'nullable|numeric',
            'intake_conversion'     => 'nullable|numeric',
            'program_cost'          => 'nullable|numeric',
            'revenue'               => 'nullable|numeric',
        ]);

        $efficiency->update($validated);

        return redirect()->route('admin.efficiency.index')
            ->with('success', 'Efficiency metric record updated successfully.');
    }

    public function destroy(EfficiencyMetric $efficiency)
    {
        $efficiency->delete();

        return redirect()->route('admin.efficiency.index')
            ->with('success', 'Efficiency metric record deleted successfully.');
    }
}
