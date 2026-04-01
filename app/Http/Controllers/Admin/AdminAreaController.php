<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AdminAreaController extends Controller
{
    public function index()
    {
        $areas = Area::ordered()->get();
        return view('admin.areas.index', compact('areas'));
    }

    public function create()
    {
        return view('admin.areas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'slug'       => 'required|string|max:255|unique:areas,slug',
            'is_statewide' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $validated['is_statewide'] = $request->has('is_statewide');

        Area::create($validated);

        return redirect()->route('admin.areas.index')
            ->with('success', 'Area created successfully.');
    }

    public function edit(Area $area)
    {
        return view('admin.areas.edit', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'slug'       => 'required|string|max:255|unique:areas,slug,' . $area->id,
            'is_statewide' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $validated['is_statewide'] = $request->has('is_statewide');

        $area->update($validated);

        return redirect()->route('admin.areas.index')
            ->with('success', 'Area updated successfully.');
    }

    public function destroy(Area $area)
    {
        $area->delete();

        return redirect()->route('admin.areas.index')
            ->with('success', 'Area deleted successfully.');
    }
}
