<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\StartingCashBalance;
use Illuminate\Http\Request;

class AdminStartingCashController extends Controller
{
    public function index()
    {
        $areas = Area::ordered()->with('startingCashBalance')->get();

        return view('admin.starting-cash.index', compact('areas'));
    }

    public function edit(Area $area)
    {
        $balance = $area->startingCashBalance ?? new StartingCashBalance([
            'area_id'    => $area->id,
            'balance'    => 0,
            'as_of_date' => now()->startOfYear()->subDay()->toDateString(),
        ]);

        return view('admin.starting-cash.edit', compact('area', 'balance'));
    }

    public function update(Request $request, Area $area)
    {
        $data = $request->validate([
            'balance'    => 'required|numeric',
            'as_of_date' => 'required|date',
            'notes'      => 'nullable|string|max:500',
        ]);

        StartingCashBalance::updateOrCreate(
            ['area_id' => $area->id],
            $data
        );

        return redirect()->route('admin.starting-cash.index')
            ->with('success', "Starting cash for {$area->name} updated.");
    }
}
