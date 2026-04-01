<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\AreaAlias;
use Illuminate\Http\Request;

class AdminAreaAliasController extends Controller
{
    public function index()
    {
        $aliases = AreaAlias::with('area')->orderBy('alias_text')->get();

        return view('admin.area-aliases.index', compact('aliases'));
    }

    public function create()
    {
        $areas = Area::ordered()->get();

        return view('admin.area-aliases.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alias_text' => 'required|string|max:255|unique:area_aliases,alias_text',
            'area_id' => 'required|exists:areas,id',
        ]);

        AreaAlias::create($request->only('alias_text', 'area_id'));

        return redirect()->route('admin.area-aliases.index')
            ->with('success', 'Area alias created.');
    }

    public function edit(AreaAlias $areaAlias)
    {
        $areas = Area::ordered()->get();

        return view('admin.area-aliases.edit', compact('areaAlias', 'areas'));
    }

    public function update(Request $request, AreaAlias $areaAlias)
    {
        $request->validate([
            'alias_text' => 'required|string|max:255|unique:area_aliases,alias_text,' . $areaAlias->id,
            'area_id' => 'required|exists:areas,id',
        ]);

        $areaAlias->update($request->only('alias_text', 'area_id'));

        return redirect()->route('admin.area-aliases.index')
            ->with('success', 'Area alias updated.');
    }

    public function destroy(AreaAlias $areaAlias)
    {
        $areaAlias->delete();

        return redirect()->route('admin.area-aliases.index')
            ->with('success', 'Area alias deleted.');
    }
}
