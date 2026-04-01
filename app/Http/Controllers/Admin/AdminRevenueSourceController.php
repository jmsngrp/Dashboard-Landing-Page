<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\RevenueSource;
use Illuminate\Http\Request;

class AdminRevenueSourceController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::ordered()->get();
        $years = RevenueSource::distinct()->pluck('fiscal_year')->sort()->values();

        $query = RevenueSource::with('area');

        if ($request->filled('year')) {
            $query->where('fiscal_year', $request->year);
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $records = $query->orderBy('fiscal_year', 'desc')
            ->orderBy('area_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.revenue-sources.index', compact('areas', 'years', 'records'));
    }

    public function create()
    {
        $areas = Area::ordered()->get();
        return view('admin.revenue-sources.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id'     => 'required|exists:areas,id',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'label'       => 'required|string|max:255',
            'amount'      => 'required|numeric',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        RevenueSource::create($validated);

        return redirect()->route('admin.revenue-sources.index')
            ->with('success', 'Revenue source created successfully.');
    }

    public function edit(RevenueSource $revenue_source)
    {
        $areas = Area::ordered()->get();
        return view('admin.revenue-sources.edit', compact('revenue_source', 'areas'));
    }

    public function update(Request $request, RevenueSource $revenue_source)
    {
        $validated = $request->validate([
            'area_id'     => 'required|exists:areas,id',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'label'       => 'required|string|max:255',
            'amount'      => 'required|numeric',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $revenue_source->update($validated);

        return redirect()->route('admin.revenue-sources.index')
            ->with('success', 'Revenue source updated successfully.');
    }

    public function destroy(RevenueSource $revenue_source)
    {
        $revenue_source->delete();

        return redirect()->route('admin.revenue-sources.index')
            ->with('success', 'Revenue source deleted successfully.');
    }
}
