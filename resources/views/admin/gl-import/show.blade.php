@extends('layouts.admin')
@section('title', 'GL Import Results')

@section('content')
<div class="page-header">
    <h1>GL Import Results</h1>
    <a href="{{ route('admin.gl-import.index') }}" class="btn btn-secondary">Back to Imports</a>
</div>

<div class="admin-card">
    <div class="form-row">
        <div class="form-group">
            <label>File</label>
            <div style="padding:8px 0; font-size:14px;">{{ $import->filename }}</div>
        </div>
        <div class="form-group">
            <label>Fiscal Year</label>
            <div style="padding:8px 0; font-size:14px;">{{ $import->fiscal_year }}</div>
        </div>
        <div class="form-group">
            <label>Status</label>
            <div style="padding:8px 0; font-size:14px;">
                @if($import->status === 'completed')
                    <span style="color:var(--green); font-weight:700;">Completed</span>
                @elseif($import->status === 'failed')
                    <span style="color:var(--rose); font-weight:700;">Failed</span>
                @else
                    {{ ucfirst($import->status) }}
                @endif
            </div>
        </div>
        <div class="form-group">
            <label>Imported</label>
            <div style="padding:8px 0; font-size:14px;">{{ $import->created_at->format('M j, Y g:ia') }}</div>
        </div>
    </div>

    <div class="form-row" style="margin-top:12px;">
        <div class="form-group">
            <label>Total Transactions</label>
            <div style="padding:8px 0; font-size:20px; font-weight:700;">{{ number_format($import->total_rows) }}</div>
        </div>
        <div class="form-group">
            <label>Area Matched</label>
            <div style="padding:8px 0; font-size:20px; font-weight:700; color:var(--green);">{{ number_format($import->matched_rows) }}</div>
        </div>
        <div class="form-group">
            <label>No Area Match</label>
            <div style="padding:8px 0; font-size:20px; font-weight:700; color:var(--rose);">{{ number_format($import->unmatched_rows) }}</div>
        </div>
        <div class="form-group">
            <label>New GL Accounts</label>
            <div style="padding:8px 0; font-size:20px; font-weight:700;">{{ $import->new_accounts }}</div>
        </div>
    </div>

    @if($import->status === 'completed')
    <div class="form-actions">
        <form method="POST" action="{{ route('admin.gl-import.recompute', $import) }}">
            @csrf
            <button type="submit" class="btn btn-primary" onclick="return confirm('This will recompute P&L values from GL data for FY{{ $import->fiscal_year }}. Continue?')">
                Recompute P&amp;L Values for FY{{ $import->fiscal_year }}
            </button>
        </form>
        <a href="{{ route('admin.gl-accounts.index', ['filter' => 'unmapped']) }}" class="btn btn-secondary">Map GL Accounts</a>
    </div>
    @endif
</div>

@if($import->error_log)
<div class="admin-card" style="margin-top:16px;">
    <h3 style="font-size:15px; font-weight:700; margin-bottom:12px; color:var(--rose);">Error Log</h3>
    <pre style="background:var(--surface2); padding:12px; border-radius:6px; font-size:12px; overflow-x:auto;">{{ $import->error_log }}</pre>
</div>
@endif

@if(count($unmatchedAreas) > 0)
<div style="margin-top:24px;">
    <h2 style="font-size:16px; font-weight:700; margin-bottom:12px;">Unmatched Area Names</h2>
    <p style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">
        These area names were found in transaction memos but don't have an alias mapping.
        <a href="{{ route('admin.area-aliases.create') }}">Create an alias</a> to map them.
    </p>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Area Text</th>
                <th class="num">Transactions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($unmatchedAreas as $ua)
            <tr>
                <td><code>{{ $ua->memo_area_raw }}</code></td>
                <td class="num">{{ $ua->count }}</td>
                <td>
                    <a href="{{ route('admin.area-aliases.create', ['alias_text' => $ua->memo_area_raw]) }}" class="btn btn-sm btn-primary">Create Alias</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($unmappedAccounts->isNotEmpty())
<div style="margin-top:24px;">
    <h2 style="font-size:16px; font-weight:700; margin-bottom:12px;">Unmapped GL Accounts</h2>
    <p style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">
        These revenue/expense accounts don't have a P&amp;L line item mapping yet.
    </p>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Account #</th>
                <th>Name</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($unmappedAccounts as $acct)
            <tr>
                <td><code>{{ $acct->account_number }}</code></td>
                <td>{{ $acct->account_name }}</td>
                <td>{{ ucfirst($acct->account_type) }}</td>
                <td>
                    <a href="{{ route('admin.gl-accounts.edit', $acct) }}" class="btn btn-sm btn-secondary">Map</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
