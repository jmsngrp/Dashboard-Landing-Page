<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\GlTransaction;
use App\Models\PnlLineItem;
use App\Models\PnlValue;
use Illuminate\Http\Request;

class AdminPnlController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::ordered()->get();
        $lineItems = PnlLineItem::ordered()->get();
        $years = PnlValue::distinct()->pluck('fiscal_year')->sort()->values();

        $selectedYear = $request->get('year', $years->last());

        // Build a lookup: [area_id][line_item_id] => amount
        $values = PnlValue::where('fiscal_year', $selectedYear)->get();
        $lookup = [];
        $sourceLookup = [];
        foreach ($values as $v) {
            $lookup[$v->area_id][$v->line_item_id] = $v->amount;
            $sourceLookup[$v->area_id][$v->line_item_id] = $v->source ?? 'manual';
        }

        // Check if GL data exists for this year
        $glYears = GlTransaction::distinct()->pluck('fiscal_year')->toArray();
        $hasGlData = in_array($selectedYear, $glYears);

        return view('admin.pnl.index', compact('areas', 'lineItems', 'years', 'selectedYear', 'lookup', 'sourceLookup', 'hasGlData'));
    }
}
