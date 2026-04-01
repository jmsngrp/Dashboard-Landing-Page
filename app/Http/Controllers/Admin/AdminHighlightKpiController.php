<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HighlightKpi;
use Illuminate\Http\Request;

class AdminHighlightKpiController extends Controller
{
    public function index()
    {
        $kpis = HighlightKpi::orderBy('sort_order')->get();
        return view('admin.highlight-kpis.index', compact('kpis'));
    }

    public function create()
    {
        return view('admin.highlight-kpis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'       => 'required|string|max:255',
            'key'         => 'required|string|max:100',
            'type'        => 'required|in:mission,cost,fin',
            'is_decimal'  => 'boolean',
            'color_class' => 'required|string|max:50',
            'sort_order'  => 'required|integer|min:0',
        ]);

        $validated['is_decimal'] = $request->boolean('is_decimal');

        HighlightKpi::create($validated);

        return redirect()->route('admin.highlight-kpis.index')->with('success', 'KPI created.');
    }

    public function edit(HighlightKpi $highlightKpi)
    {
        return view('admin.highlight-kpis.edit', ['kpi' => $highlightKpi]);
    }

    public function update(Request $request, HighlightKpi $highlightKpi)
    {
        $validated = $request->validate([
            'label'       => 'required|string|max:255',
            'key'         => 'required|string|max:100',
            'type'        => 'required|in:mission,cost,fin',
            'is_decimal'  => 'boolean',
            'color_class' => 'required|string|max:50',
            'sort_order'  => 'required|integer|min:0',
        ]);

        $validated['is_decimal'] = $request->boolean('is_decimal');

        $highlightKpi->update($validated);

        return redirect()->route('admin.highlight-kpis.index')->with('success', 'KPI updated.');
    }

    public function destroy(HighlightKpi $highlightKpi)
    {
        $highlightKpi->delete();
        return redirect()->route('admin.highlight-kpis.index')->with('success', 'KPI deleted.');
    }
}
