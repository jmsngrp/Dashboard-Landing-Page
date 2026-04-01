<?php

namespace App\Http\Controllers;

use App\Models\GlAccount;
use App\Models\GlTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlDrilldownController extends Controller
{
    /**
     * Get GL sub-accounts for a P&L line item, with summed amounts.
     */
    public function lineItemDetail(Request $request): JsonResponse
    {
        $request->validate([
            'line_item_id' => 'required|integer',
            'fiscal_year' => 'required|integer',
            'area_id' => 'nullable|integer',
        ]);

        $lineItemId = $request->line_item_id;
        $fiscalYear = $request->fiscal_year;
        $areaId = $request->area_id;

        // Find GL accounts mapped to this P&L line item
        $accountIds = GlAccount::where('pnl_line_item_id', $lineItemId)
            ->pluck('id');

        if ($accountIds->isEmpty()) {
            return response()->json(['accounts' => []]);
        }

        // Get all fiscal years that have GL data for context
        $years = GlTransaction::whereIn('gl_account_id', $accountIds)
            ->distinct()
            ->pluck('fiscal_year')
            ->sort()
            ->values();

        // Build query for sub-account totals
        $query = DB::table('gl_transactions')
            ->join('gl_accounts', 'gl_transactions.gl_account_id', '=', 'gl_accounts.id')
            ->whereIn('gl_transactions.gl_account_id', $accountIds)
            ->groupBy('gl_accounts.id', 'gl_accounts.account_number', 'gl_accounts.account_name', 'gl_transactions.fiscal_year')
            ->select(
                'gl_accounts.id',
                'gl_accounts.account_number',
                'gl_accounts.account_name',
                'gl_transactions.fiscal_year',
                DB::raw('SUM(gl_transactions.amount) as total'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->orderBy('gl_accounts.account_number');

        if ($areaId) {
            $query->where('gl_transactions.area_id', $areaId);
        }

        $results = $query->get();

        // Pivot: group by account, nest years
        $accounts = [];
        foreach ($results as $row) {
            if (!isset($accounts[$row->id])) {
                $accounts[$row->id] = [
                    'id' => $row->id,
                    'account_number' => $row->account_number,
                    'account_name' => $row->account_name,
                    'years' => [],
                ];
            }
            $accounts[$row->id]['years'][$row->fiscal_year] = [
                'total' => round((float) $row->total, 2),
                'count' => $row->transaction_count,
            ];
        }

        return response()->json([
            'accounts' => array_values($accounts),
            'years' => $years,
        ]);
    }

    /**
     * Get individual transactions for a GL account.
     */
    public function accountTransactions(Request $request): JsonResponse
    {
        $request->validate([
            'gl_account_id' => 'required|integer',
            'fiscal_year' => 'required|integer',
            'area_id' => 'nullable|integer',
        ]);

        $query = GlTransaction::with('area')
            ->where('gl_account_id', $request->gl_account_id)
            ->where('fiscal_year', $request->fiscal_year)
            ->orderBy('transaction_date');

        if ($request->area_id) {
            $query->where('area_id', $request->area_id);
        }

        $transactions = $query->limit(500)->get()->map(function ($txn) {
            return [
                'date' => $txn->transaction_date->format('m/d/Y'),
                'type' => $txn->type,
                'name' => $txn->name,
                'memo' => $txn->memo,
                'area' => $txn->area?->name,
                'amount' => round((float) $txn->amount, 2),
            ];
        });

        return response()->json(['transactions' => $transactions]);
    }
}
