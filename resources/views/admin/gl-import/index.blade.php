@extends('layouts.admin')
@section('title', 'GL Import')

@section('content')
<div class="page-header">
    <h1>QBO General Ledger Import</h1>
</div>

@if($errors->any())
<div style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; padding:12px 18px; border-radius:6px; margin-bottom:20px; font-size:13.5px;">
    @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
</div>
@endif

<div class="admin-card">
    <form method="POST" action="{{ route('admin.gl-import.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="gl_file">GL Report (.xlsx)</label>
                <input type="file" name="gl_file" id="gl_file" class="form-control" accept=".xlsx,.xls" required>
            </div>
            <div class="form-group">
                <label for="fiscal_year">Fiscal Year</label>
                <input type="number" name="fiscal_year" id="fiscal_year" class="form-control"
                       value="{{ old('fiscal_year', date('Y')) }}" min="2000" max="2100" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Import GL Report</button>
        </div>
    </form>
</div>

@if($imports->isNotEmpty())
<div style="margin-top:24px;">
    <h2 style="font-size:16px; font-weight:700; margin-bottom:12px;">Import History</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>File</th>
                <th>FY</th>
                <th class="num">Rows</th>
                <th class="num">Matched</th>
                <th class="num">Unmatched</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($imports as $import)
            <tr>
                <td>{{ $import->created_at->format('M j, Y g:ia') }}</td>
                <td>{{ $import->filename }}</td>
                <td>{{ $import->fiscal_year }}</td>
                <td class="num">{{ number_format($import->total_rows) }}</td>
                <td class="num">{{ number_format($import->matched_rows) }}</td>
                <td class="num">{{ number_format($import->unmatched_rows) }}</td>
                <td>
                    @if($import->status === 'completed')
                        <span style="color:var(--green); font-weight:700;">Completed</span>
                    @elseif($import->status === 'failed')
                        <span style="color:var(--rose); font-weight:700;">Failed</span>
                    @else
                        <span style="color:var(--text-muted);">{{ ucfirst($import->status) }}</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.gl-import.show', $import) }}" class="btn btn-sm btn-secondary">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
