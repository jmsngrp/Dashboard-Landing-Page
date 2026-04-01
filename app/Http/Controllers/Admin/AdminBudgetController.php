<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Budget;
use Illuminate\Http\Request;

class AdminBudgetController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::ordered()->get();
        $years = Budget::distinct()->pluck('fiscal_year')->sort()->values();

        $query = Budget::with('area');

        if ($request->filled('year')) {
            $query->where('fiscal_year', $request->year);
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $records = $query->orderBy('fiscal_year', 'desc')->orderBy('area_id')->get();

        return view('admin.budgets.index', compact('areas', 'years', 'records'));
    }

    public function create()
    {
        $areas = Area::ordered()->get();
        return view('admin.budgets.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id'              => 'required|exists:areas,id',
            'fiscal_year'          => 'required|integer|min:2000|max:2100',
            'revenue'              => 'nullable|numeric',
            'individual_donations' => 'nullable|numeric',
            'church_giving'        => 'nullable|numeric',
            'grant_revenue'        => 'nullable|numeric',
            'foundation_revenue'   => 'nullable|numeric',
            'fundraising_events'   => 'nullable|numeric',
            'institutional'        => 'nullable|numeric',
            'cogs'                 => 'nullable|numeric',
            'program_costs'        => 'nullable|numeric',
            'admin_costs'          => 'nullable|numeric',
            'total_expenses'       => 'nullable|numeric',
            'gross_profit'         => 'nullable|numeric',
            'net_operating'        => 'nullable|numeric',
            'rev_sharing'          => 'nullable|numeric',
            'net_revenue'          => 'nullable|numeric',
        ]);

        Budget::create($validated);

        return redirect()->route('admin.budgets.index')
            ->with('success', 'Budget record created successfully.');
    }

    public function edit(Budget $budget)
    {
        $areas = Area::ordered()->get();
        return view('admin.budgets.edit', compact('budget', 'areas'));
    }

    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'area_id'              => 'required|exists:areas,id',
            'fiscal_year'          => 'required|integer|min:2000|max:2100',
            'revenue'              => 'nullable|numeric',
            'individual_donations' => 'nullable|numeric',
            'church_giving'        => 'nullable|numeric',
            'grant_revenue'        => 'nullable|numeric',
            'foundation_revenue'   => 'nullable|numeric',
            'fundraising_events'   => 'nullable|numeric',
            'institutional'        => 'nullable|numeric',
            'cogs'                 => 'nullable|numeric',
            'program_costs'        => 'nullable|numeric',
            'admin_costs'          => 'nullable|numeric',
            'total_expenses'       => 'nullable|numeric',
            'gross_profit'         => 'nullable|numeric',
            'net_operating'        => 'nullable|numeric',
            'rev_sharing'          => 'nullable|numeric',
            'net_revenue'          => 'nullable|numeric',
        ]);

        $budget->update($validated);

        return redirect()->route('admin.budgets.index')
            ->with('success', 'Budget record updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();

        return redirect()->route('admin.budgets.index')
            ->with('success', 'Budget record deleted successfully.');
    }
}
