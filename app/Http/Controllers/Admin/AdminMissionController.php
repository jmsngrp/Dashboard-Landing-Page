<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\MissionMetric;
use Illuminate\Http\Request;

class AdminMissionController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::ordered()->get();
        $years = MissionMetric::distinct()->pluck('fiscal_year')->sort()->values();

        $query = MissionMetric::with('area');

        if ($request->filled('year')) {
            $query->where('fiscal_year', $request->year);
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $records = $query->orderBy('fiscal_year', 'desc')->orderBy('area_id')->get();

        return view('admin.mission.index', compact('areas', 'years', 'records'));
    }

    public function create()
    {
        $areas = Area::ordered()->get();
        return view('admin.mission.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id'              => 'required|exists:areas,id',
            'fiscal_year'          => 'required|integer|min:2000|max:2100',
            'families_served'      => 'nullable|integer',
            'individuals_served'   => 'nullable|integer',
            'avg_monthly_families' => 'nullable|numeric',
            'hosted_days'          => 'nullable|integer',
            'hosted_nights'        => 'nullable|integer',
            'total_hosted'         => 'nullable|integer',
            'total_volunteers'     => 'nullable|integer',
            'partner_churches'     => 'nullable|integer',
            'service_hours'        => 'nullable|integer',
            'intake'               => 'nullable|integer',
            'opened'               => 'nullable|integer',
            'graduations'          => 'nullable|integer',
            'total_relationships'  => 'nullable|integer',
        ]);

        MissionMetric::create($validated);

        return redirect()->route('admin.mission.index')
            ->with('success', 'Mission metric record created successfully.');
    }

    public function edit(MissionMetric $mission)
    {
        $areas = Area::ordered()->get();
        return view('admin.mission.edit', compact('mission', 'areas'));
    }

    public function update(Request $request, MissionMetric $mission)
    {
        $validated = $request->validate([
            'area_id'              => 'required|exists:areas,id',
            'fiscal_year'          => 'required|integer|min:2000|max:2100',
            'families_served'      => 'nullable|integer',
            'individuals_served'   => 'nullable|integer',
            'avg_monthly_families' => 'nullable|numeric',
            'hosted_days'          => 'nullable|integer',
            'hosted_nights'        => 'nullable|integer',
            'total_hosted'         => 'nullable|integer',
            'total_volunteers'     => 'nullable|integer',
            'partner_churches'     => 'nullable|integer',
            'service_hours'        => 'nullable|integer',
            'intake'               => 'nullable|integer',
            'opened'               => 'nullable|integer',
            'graduations'          => 'nullable|integer',
            'total_relationships'  => 'nullable|integer',
        ]);

        $mission->update($validated);

        return redirect()->route('admin.mission.index')
            ->with('success', 'Mission metric record updated successfully.');
    }

    public function destroy(MissionMetric $mission)
    {
        $mission->delete();

        return redirect()->route('admin.mission.index')
            ->with('success', 'Mission metric record deleted successfully.');
    }
}
