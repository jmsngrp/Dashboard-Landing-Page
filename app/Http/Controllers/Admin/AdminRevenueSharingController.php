<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\RevenueSharing;
use Illuminate\Http\Request;

class AdminRevenueSharingController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::ordered()->get();
        $years = RevenueSharing::distinct()->pluck('fiscal_year')->sort()->values();

        $query = RevenueSharing::with('area');

        if ($request->filled('year')) {
            $query->where('fiscal_year', $request->year);
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $records = $query->orderBy('fiscal_year', 'desc')->orderBy('area_id')->get();

        return view('admin.revenue-sharing.index', compact('areas', 'years', 'records'));
    }

    public function create()
    {
        $areas = Area::ordered()->get();
        return view('admin.revenue-sharing.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id'     => 'required|exists:areas,id',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'amount'      => 'required|numeric',
        ]);

        RevenueSharing::create($validated);

        return redirect()->route('admin.revenue-sharing.index')
            ->with('success', 'Revenue sharing record created successfully.');
    }

    public function edit(RevenueSharing $revenue_sharing)
    {
        $areas = Area::ordered()->get();
        return view('admin.revenue-sharing.edit', compact('revenue_sharing', 'areas'));
    }

    public function update(Request $request, RevenueSharing $revenue_sharing)
    {
        $validated = $request->validate([
            'area_id'     => 'required|exists:areas,id',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'amount'      => 'required|numeric',
        ]);

        $revenue_sharing->update($validated);

        return redirect()->route('admin.revenue-sharing.index')
            ->with('success', 'Revenue sharing record updated successfully.');
    }

    public function destroy(RevenueSharing $revenue_sharing)
    {
        $revenue_sharing->delete();

        return redirect()->route('admin.revenue-sharing.index')
            ->with('success', 'Revenue sharing record deleted successfully.');
    }
}
