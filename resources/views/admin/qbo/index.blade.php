@extends('layouts.admin')
@section('title', 'QuickBooks Online')

@section('content')
<div class="page-header">
    <h1>QuickBooks Online Integration</h1>
</div>

{{-- ── Connection Status ─────────────────────────────────────── --}}
<div class="admin-card">
    <h2 style="font-size:16px; font-weight:700; margin-bottom:16px;">Connection Status</h2>

    @if($token)
        {{-- Connected --}}
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:var(--green);"></span>
            <span style="font-weight:600; color:var(--green);">Connected</span>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px;">
            <div>
                <div style="font-size:12px; font-weight:600; color:var(--text-muted); margin-bottom:2px;">Company</div>
                <div style="font-size:14px;">{{ $token->company_name ?: 'Unknown' }}</div>
            </div>
            <div>
                <div style="font-size:12px; font-weight:600; color:var(--text-muted); margin-bottom:2px;">Realm ID</div>
                <div style="font-size:14px; font-family:monospace;">{{ $token->realm_id }}</div>
            </div>
            <div>
                <div style="font-size:12px; font-weight:600; color:var(--text-muted); margin-bottom:2px;">Connected By</div>
                <div style="font-size:14px;">{{ $token->connectedBy->name ?? 'Unknown' }}</div>
            </div>
            <div>
                <div style="font-size:12px; font-weight:600; color:var(--text-muted); margin-bottom:2px;">Token Status</div>
                <div style="font-size:14px;">
                    @if($token->isHealthy())
                        <span style="color:var(--green);">Healthy</span>
                        <span style="color:var(--text-dim); font-size:12px;">
                            &mdash; refresh expires {{ $token->refresh_token_expires_at->diffForHumans() }}
                        </span>
                    @else
                        <span style="color:var(--rose); font-weight:600;">Expired &mdash; Please reconnect</span>
                    @endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.qbo.disconnect') }}" style="display:inline;"
              onsubmit="return confirm('Disconnect from QuickBooks? You will need to re-authorize to sync again.')">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">Disconnect</button>
        </form>

    @elseif($hasCredentials)
        {{-- Not connected but credentials configured --}}
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:var(--text-dim);"></span>
            <span style="font-weight:600; color:var(--text-muted);">Not Connected</span>
        </div>
        <p style="color:var(--text-muted); margin-bottom:16px; font-size:13.5px;">
            QuickBooks credentials are configured. Click below to authorize this application.
        </p>
        <a href="{{ route('admin.qbo.connect') }}" class="btn btn-primary">Connect to QuickBooks</a>

    @else
        {{-- No credentials configured --}}
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:var(--rose);"></span>
            <span style="font-weight:600; color:var(--rose);">Not Configured</span>
        </div>
        <div style="background:var(--surface2); border-radius:6px; padding:20px; font-size:13.5px; line-height:1.8;">
            <strong>Setup Instructions:</strong>
            <ol style="margin:10px 0 0 20px;">
                <li>Create an <a href="https://developer.intuit.com" target="_blank" style="color:var(--accent);">Intuit Developer</a> account</li>
                <li>Create an app &rarr; select "QuickBooks Online and Payments"</li>
                <li>Under "Keys &amp; credentials", copy the <strong>Client ID</strong> and <strong>Client Secret</strong></li>
                <li>Set Redirect URI to: <code style="background:var(--surface3); padding:2px 6px; border-radius:3px; font-size:12px;">{{ config('quickbooks.redirect_uri', url('/admin/qbo/callback')) }}</code></li>
                <li>Add to your <code>.env</code> file:
                    <pre style="background:var(--surface3); padding:12px; border-radius:5px; margin-top:6px; font-size:12px; overflow-x:auto;">QBO_CLIENT_ID=your_client_id
QBO_CLIENT_SECRET=your_client_secret
QBO_REDIRECT_URI={{ config('quickbooks.redirect_uri', url('/admin/qbo/callback')) }}
QBO_ENVIRONMENT=sandbox</pre>
                </li>
                <li>Restart your server, then return here to connect</li>
            </ol>
        </div>
    @endif
</div>

{{-- ── Sync Form (only when connected) ───────────────────────── --}}
@if($token && $token->isHealthy())
<div class="admin-card" style="margin-top:24px;">
    <h2 style="font-size:16px; font-weight:700; margin-bottom:16px;">Sync GL Data</h2>
    <p style="color:var(--text-muted); margin-bottom:16px; font-size:13.5px;">
        Pull General Ledger transactions from QuickBooks. This will <strong>replace</strong> existing
        transactions for the selected fiscal year (same as XLSX import behavior).
    </p>
    <form method="POST" action="{{ route('admin.qbo.sync') }}">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="fiscal_year">Fiscal Year</label>
                <input type="number" name="fiscal_year" id="fiscal_year" class="form-control"
                       value="{{ old('fiscal_year', date('Y')) }}" min="2000" max="2100" required>
            </div>
            <div class="form-group">
                <label for="start_date">Start Date <span style="color:var(--text-dim); font-weight:400;">(optional)</span></label>
                <input type="date" name="start_date" id="start_date" class="form-control"
                       value="{{ old('start_date') }}">
            </div>
            <div class="form-group">
                <label for="end_date">End Date <span style="color:var(--text-dim); font-weight:400;">(optional)</span></label>
                <input type="date" name="end_date" id="end_date" class="form-control"
                       value="{{ old('end_date') }}">
            </div>
        </div>
        <p style="font-size:12px; color:var(--text-dim); margin-top:4px;">
            If dates are omitted, defaults to Jan 1 &ndash; Dec 31 of the fiscal year.
        </p>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"
                    onclick="this.disabled=true; this.innerText='Syncing...'; this.closest('form').submit();">
                Sync from QuickBooks
            </button>
        </div>
    </form>
</div>
@endif

{{-- ── Recent API Syncs ──────────────────────────────────────── --}}
@if($recentSyncs->isNotEmpty())
<div style="margin-top:24px;">
    <h2 style="font-size:16px; font-weight:700; margin-bottom:12px;">Recent API Syncs</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Range</th>
                <th>FY</th>
                <th class="num">Rows</th>
                <th class="num">Matched</th>
                <th class="num">Unmatched</th>
                <th class="num">New Accts</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentSyncs as $sync)
            <tr>
                <td>{{ $sync->created_at->format('M j, Y g:ia') }}</td>
                <td>
                    @if($sync->sync_start_date && $sync->sync_end_date)
                        {{ \Carbon\Carbon::parse($sync->sync_start_date)->format('M j') }}
                        &ndash;
                        {{ \Carbon\Carbon::parse($sync->sync_end_date)->format('M j, Y') }}
                    @else
                        &mdash;
                    @endif
                </td>
                <td>{{ $sync->fiscal_year }}</td>
                <td class="num">{{ number_format($sync->total_rows ?? 0) }}</td>
                <td class="num">{{ number_format($sync->matched_rows ?? 0) }}</td>
                <td class="num">{{ number_format($sync->unmatched_rows ?? 0) }}</td>
                <td class="num">{{ number_format($sync->new_accounts ?? 0) }}</td>
                <td>
                    @if($sync->status === 'completed')
                        <span style="color:var(--green); font-weight:700;">Completed</span>
                    @elseif($sync->status === 'failed')
                        <span style="color:var(--rose); font-weight:700;">Failed</span>
                    @elseif($sync->status === 'processing')
                        <span style="color:var(--accent); font-weight:700;">Processing</span>
                    @else
                        <span style="color:var(--text-muted);">{{ ucfirst($sync->status) }}</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.gl-import.show', $sync) }}" class="btn btn-sm btn-secondary">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
