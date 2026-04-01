<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlImport;
use App\Models\QboToken;
use App\Services\GlImportService;
use App\Services\QboClient;
use App\Services\QboSyncService;
use Illuminate\Http\Request;

class AdminQboController extends Controller
{
    /**
     * Show QBO connection status and sync interface.
     */
    public function index()
    {
        $token = QboToken::current();
        $hasCredentials = ! empty(config('quickbooks.client_id'));

        // Get recent QBO syncs
        $recentSyncs = GlImport::where('source', 'qbo_api')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.qbo.index', compact('token', 'hasCredentials', 'recentSyncs'));
    }

    /**
     * Redirect to QBO OAuth authorization page.
     */
    public function connect(QboClient $client)
    {
        if (empty(config('quickbooks.client_id'))) {
            return redirect()->route('admin.qbo.index')
                ->with('error', 'QuickBooks credentials not configured. Add QBO_CLIENT_ID and QBO_CLIENT_SECRET to your .env file.');
        }

        try {
            $authUrl = $client->getAuthorizationUrl();

            return redirect()->away($authUrl);
        } catch (\Throwable $e) {
            return redirect()->route('admin.qbo.index')
                ->with('error', 'Failed to generate authorization URL: ' . $e->getMessage());
        }
    }

    /**
     * OAuth callback handler. Exchange code for tokens.
     */
    public function callback(Request $request, QboClient $client)
    {
        $code = $request->query('code');
        $realmId = $request->query('realmId');

        if (! $code || ! $realmId) {
            return redirect()->route('admin.qbo.index')
                ->with('error', 'Authorization failed: missing code or company ID from QuickBooks.');
        }

        try {
            $tokenData = $client->exchangeCodeForTokens($code, $realmId);

            // Delete any existing connection (singleton pattern)
            QboToken::truncate();

            $token = QboToken::create(array_merge($tokenData, [
                'connected_by' => auth()->id(),
            ]));

            // Try to fetch company name for display
            try {
                $companyName = $client->getCompanyName();
                if ($companyName) {
                    $token->update(['company_name' => $companyName]);
                }
            } catch (\Throwable) {
                // Non-critical — company name is cosmetic
            }

            return redirect()->route('admin.qbo.index')
                ->with('success', 'Connected to QuickBooks Online' .
                    ($token->company_name ? " ({$token->company_name})" : '') . '.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.qbo.index')
                ->with('error', 'Failed to connect: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect from QBO (delete tokens).
     */
    public function disconnect()
    {
        QboToken::truncate();

        return redirect()->route('admin.qbo.index')
            ->with('success', 'Disconnected from QuickBooks Online.');
    }

    /**
     * Sync GL data from QBO.
     */
    public function sync(Request $request, QboClient $client)
    {
        $request->validate([
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $syncService = new QboSyncService($client);

        $import = $syncService->sync(
            (int) $request->fiscal_year,
            auth()->id(),
            $request->start_date,
            $request->end_date
        );

        if ($import->status === 'failed') {
            return redirect()->route('admin.qbo.index')
                ->with('error', 'Sync failed: ' . $import->error_log);
        }

        return redirect()->route('admin.gl-import.show', $import)
            ->with('success', "Synced {$import->total_rows} transactions from QuickBooks.");
    }
}
