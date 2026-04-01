<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlAccount;
use App\Models\PnlLineItem;
use App\Services\GlImportService;
use Illuminate\Http\Request;

class AdminGlAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = GlAccount::with('pnlLineItem')->ordered();

        $filter = $request->get('filter', 'all');
        if ($filter === 'mapped') {
            $query->mapped();
        } elseif ($filter === 'unmapped') {
            $query->unmapped();
        }

        $typeFilter = $request->get('type', 'all');
        if ($typeFilter !== 'all') {
            $query->where('account_type', $typeFilter);
        }

        $accounts = $query->get();
        $lineItems = PnlLineItem::ordered()->get();

        return view('admin.gl-accounts.index', compact('accounts', 'lineItems', 'filter', 'typeFilter'));
    }

    public function edit(GlAccount $glAccount)
    {
        $lineItems = PnlLineItem::ordered()->get()->groupBy('category');

        return view('admin.gl-accounts.edit', compact('glAccount', 'lineItems'));
    }

    public function update(Request $request, GlAccount $glAccount)
    {
        $request->validate([
            'pnl_line_item_id' => 'nullable|exists:pnl_line_items,id',
        ]);

        $glAccount->update([
            'pnl_line_item_id' => $request->pnl_line_item_id ?: null,
        ]);

        return redirect()->route('admin.gl-accounts.index')
            ->with('success', "Mapping updated for {$glAccount->account_number} {$glAccount->account_name}.");
    }

    public function autoMap(GlImportService $service)
    {
        $mapped = $service->autoMapAccounts();

        return redirect()->route('admin.gl-accounts.index')
            ->with('success', "Auto-mapped {$mapped} GL accounts to P&L line items.");
    }
}
