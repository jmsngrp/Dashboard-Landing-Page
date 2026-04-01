<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetBucket;
use Illuminate\Http\Request;

class AdminBudgetBucketController extends Controller
{
    public function index()
    {
        $buckets = BudgetBucket::ordered()->get()->groupBy('category');

        return view('admin.budget-buckets.index', compact('buckets'));
    }

    public function create()
    {
        return view('admin.budget-buckets.form', [
            'bucket'     => null,
            'categories' => $this->categoryOptions(),
            'nextSort'   => BudgetBucket::max('sort_order') + 1,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'category'        => 'required|in:revenue,cogs,program,admin,summary',
            'is_summary'      => 'boolean',
            'summary_formula' => 'nullable|string|max:255',
            'semantic_key'    => 'nullable|string|max:100|unique:budget_buckets,semantic_key',
            'sort_order'      => 'required|integer|min:0',
            'is_active'       => 'boolean',
        ]);

        $data['is_summary'] = $request->boolean('is_summary');
        $data['is_active'] = $request->boolean('is_active', true);

        BudgetBucket::create($data);

        return redirect()->route('admin.budget-buckets.index')
            ->with('success', 'Budget bucket created.');
    }

    public function edit(BudgetBucket $budgetBucket)
    {
        return view('admin.budget-buckets.form', [
            'bucket'     => $budgetBucket,
            'categories' => $this->categoryOptions(),
            'nextSort'   => null,
        ]);
    }

    public function update(Request $request, BudgetBucket $budgetBucket)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'category'        => 'required|in:revenue,cogs,program,admin,summary',
            'is_summary'      => 'boolean',
            'summary_formula' => 'nullable|string|max:255',
            'semantic_key'    => 'nullable|string|max:100|unique:budget_buckets,semantic_key,' . $budgetBucket->id,
            'sort_order'      => 'required|integer|min:0',
            'is_active'       => 'boolean',
        ]);

        $data['is_summary'] = $request->boolean('is_summary');
        $data['is_active'] = $request->boolean('is_active', true);

        // Clear formula if not a summary row
        if (! $data['is_summary']) {
            $data['summary_formula'] = null;
        }

        $budgetBucket->update($data);

        return redirect()->route('admin.budget-buckets.index')
            ->with('success', 'Budget bucket updated.');
    }

    public function destroy(BudgetBucket $budgetBucket)
    {
        // Prevent deleting if amounts or GL accounts reference this bucket
        if ($budgetBucket->amounts()->exists() || $budgetBucket->glAccounts()->exists()) {
            return redirect()->route('admin.budget-buckets.index')
                ->with('error', 'Cannot delete bucket with existing amounts or GL account mappings.');
        }

        $budgetBucket->delete();

        return redirect()->route('admin.budget-buckets.index')
            ->with('success', 'Budget bucket deleted.');
    }

    private function categoryOptions(): array
    {
        return [
            'revenue' => 'Revenue',
            'cogs'    => 'Cost of Goods Sold',
            'program' => 'Program Costs',
            'admin'   => 'Admin Costs',
            'summary' => 'Summary / Computed',
        ];
    }
}
