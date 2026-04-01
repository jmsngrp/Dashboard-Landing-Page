<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlImport;
use App\Services\GlImportService;
use Illuminate\Http\Request;

class AdminGlImportController extends Controller
{
    public function index()
    {
        $imports = GlImport::with('user')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.gl-import.index', compact('imports'));
    }

    public function store(Request $request, GlImportService $service)
    {
        $request->validate([
            'gl_file' => 'required|file|mimes:xlsx,xls|max:51200',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
        ]);

        $file = $request->file('gl_file');
        $path = $file->storeAs('gl-imports', $file->getClientOriginalName());
        $fullPath = storage_path('app/' . $path);

        $import = $service->import($fullPath, $request->fiscal_year, auth()->id());

        if ($import->status === 'failed') {
            return redirect()->route('admin.gl-import.index')
                ->with('error', 'Import failed: ' . $import->error_log);
        }

        return redirect()->route('admin.gl-import.show', $import)
            ->with('success', "Imported {$import->total_rows} transactions.");
    }

    public function show(GlImport $import, GlImportService $service)
    {
        $unmatchedAreas = $service->getUnmatchedAreas($import->fiscal_year);

        $unmappedAccounts = \App\Models\GlAccount::whereNull('pnl_line_item_id')
            ->where('is_active', true)
            ->whereIn('account_type', ['revenue', 'expense'])
            ->ordered()
            ->get();

        return view('admin.gl-import.show', compact('import', 'unmatchedAreas', 'unmappedAccounts'));
    }

    public function recompute(GlImport $import, GlImportService $service)
    {
        $stats = $service->recomputePnlValues($import->fiscal_year);

        if (isset($stats['error'])) {
            return back()->with('error', $stats['error']);
        }

        return back()->with('success', "Recomputed P&L values: {$stats['updated']} values updated across {$stats['areas']} areas.");
    }
}
