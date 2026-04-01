<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HighlightGroup;
use App\Models\HighlightKpi;
use Illuminate\Http\Request;

class AdminHighlightController extends Controller
{
    public function index()
    {
        $groups = HighlightGroup::orderBy('sort_order')->with('kpis')->get();
        return view('admin.highlights.index', compact('groups'));
    }

    public function create()
    {
        $kpis = HighlightKpi::orderBy('sort_order')->get();
        return view('admin.highlights.create', compact('kpis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'subtitle'   => 'nullable|string|max:500',
            'color'      => 'required|string|max:50',
            'sort_order' => 'required|integer|min:0',
            'kpis'       => 'nullable|array',
            'kpis.*'     => 'exists:highlight_kpis,id',
        ]);

        $group = HighlightGroup::create([
            'title'      => $validated['title'],
            'subtitle'   => $validated['subtitle'] ?? null,
            'color'      => $validated['color'],
            'sort_order' => $validated['sort_order'],
        ]);

        if (! empty($validated['kpis'])) {
            $syncData = [];
            foreach ($validated['kpis'] as $i => $kpiId) {
                $syncData[$kpiId] = ['sort_order' => $i + 1];
            }
            $group->kpis()->sync($syncData);
        }

        return redirect()->route('admin.highlights.index')->with('success', 'Highlight group created.');
    }

    public function edit(HighlightGroup $highlight)
    {
        $kpis = HighlightKpi::orderBy('sort_order')->get();
        $selectedKpiIds = $highlight->kpis->pluck('id')->toArray();
        return view('admin.highlights.edit', compact('highlight', 'kpis', 'selectedKpiIds'));
    }

    public function update(Request $request, HighlightGroup $highlight)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'subtitle'   => 'nullable|string|max:500',
            'color'      => 'required|string|max:50',
            'sort_order' => 'required|integer|min:0',
            'kpis'       => 'nullable|array',
            'kpis.*'     => 'exists:highlight_kpis,id',
        ]);

        $highlight->update([
            'title'      => $validated['title'],
            'subtitle'   => $validated['subtitle'] ?? null,
            'color'      => $validated['color'],
            'sort_order' => $validated['sort_order'],
        ]);

        $syncData = [];
        foreach (($validated['kpis'] ?? []) as $i => $kpiId) {
            $syncData[$kpiId] = ['sort_order' => $i + 1];
        }
        $highlight->kpis()->sync($syncData);

        return redirect()->route('admin.highlights.index')->with('success', 'Highlight group updated.');
    }

    public function destroy(HighlightGroup $highlight)
    {
        $highlight->delete();
        return redirect()->route('admin.highlights.index')->with('success', 'Highlight group deleted.');
    }
}
