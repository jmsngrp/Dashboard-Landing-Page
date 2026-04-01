<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\LocalFundraising;
use Illuminate\Http\Request;

class AdminLocalFundraisingController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::ordered()->get();
        $years = LocalFundraising::distinct()->pluck('fiscal_year')->sort()->values();

        $query = LocalFundraising::with('area');

        if ($request->filled('year')) {
            $query->where('fiscal_year', $request->year);
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $records = $query->orderBy('fiscal_year', 'desc')->orderBy('area_id')->get();

        return view('admin.local-fundraising.index', compact('areas', 'years', 'records'));
    }

    public function create()
    {
        $areas = Area::ordered()->get();
        return view('admin.local-fundraising.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id'     => 'required|exists:areas,id',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'amount'      => 'required|numeric',
        ]);

        LocalFundraising::create($validated);

        return redirect()->route('admin.local-fundraising.index')
            ->with('success', 'Local fundraising record created successfully.');
    }

    public function edit(LocalFundraising $local_fundraising)
    {
        $areas = Area::ordered()->get();
        return view('admin.local-fundraising.edit', compact('local_fundraising', 'areas'));
    }

    public function update(Request $request, LocalFundraising $local_fundraising)
    {
        $validated = $request->validate([
            'area_id'     => 'required|exists:areas,id',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'amount'      => 'required|numeric',
        ]);

        $local_fundraising->update($validated);

        return redirect()->route('admin.local-fundraising.index')
            ->with('success', 'Local fundraising record updated successfully.');
    }

    public function destroy(LocalFundraising $local_fundraising)
    {
        $local_fundraising->delete();

        return redirect()->route('admin.local-fundraising.index')
            ->with('success', 'Local fundraising record deleted successfully.');
    }
}
