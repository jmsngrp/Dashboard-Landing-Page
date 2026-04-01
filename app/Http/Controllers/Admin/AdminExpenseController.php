<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\ExpenseSummary;
use Illuminate\Http\Request;

class AdminExpenseController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::ordered()->get();
        $years = ExpenseSummary::distinct()->pluck('fiscal_year')->sort()->values();

        $query = ExpenseSummary::with('area');

        if ($request->filled('year')) {
            $query->where('fiscal_year', $request->year);
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $records = $query->orderBy('fiscal_year', 'desc')->orderBy('area_id')->get();

        return view('admin.expenses.index', compact('areas', 'years', 'records'));
    }

    public function create()
    {
        $areas = Area::ordered()->get();
        return view('admin.expenses.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id'      => 'required|exists:areas,id',
            'fiscal_year'  => 'required|integer|min:2000|max:2100',
            'program'      => 'nullable|numeric',
            'admin'        => 'nullable|numeric',
            'fundraising'  => 'nullable|numeric',
            'total'        => 'nullable|numeric',
        ]);

        ExpenseSummary::create($validated);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense summary created successfully.');
    }

    public function edit(ExpenseSummary $expense)
    {
        $areas = Area::ordered()->get();
        return view('admin.expenses.edit', compact('expense', 'areas'));
    }

    public function update(Request $request, ExpenseSummary $expense)
    {
        $validated = $request->validate([
            'area_id'      => 'required|exists:areas,id',
            'fiscal_year'  => 'required|integer|min:2000|max:2100',
            'program'      => 'nullable|numeric',
            'admin'        => 'nullable|numeric',
            'fundraising'  => 'nullable|numeric',
            'total'        => 'nullable|numeric',
        ]);

        $expense->update($validated);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense summary updated successfully.');
    }

    public function destroy(ExpenseSummary $expense)
    {
        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense summary deleted successfully.');
    }
}
