@extends('layouts.admin')
@section('title', 'Budget Buckets')

@section('content')
<div class="page-header">
    <h1>Budget Buckets</h1>
    <a href="{{ route('admin.budget-buckets.create') }}" class="btn btn-primary">+ Add Bucket</a>
</div>

@if(session('success'))
    <div style="padding:10px 16px; background:#e8f5e9; border-radius:6px; color:#2e7d32; margin-bottom:16px; font-size:13px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="padding:10px 16px; background:#fce4ec; border-radius:6px; color:#c62828; margin-bottom:16px; font-size:13px;">
        {{ session('error') }}
    </div>
@endif

@php
    $categoryLabels = [
        'revenue' => 'Revenue',
        'cogs'    => 'Cost of Goods Sold',
        'program' => 'Program Costs',
        'admin'   => 'Admin Costs',
        'summary' => 'Summary / Computed',
    ];
    $categoryColors = [
        'revenue' => '#6b9146',
        'cogs'    => '#b09030',
        'program' => '#4a88b0',
        'admin'   => '#7b649a',
        'summary' => '#787774',
    ];
@endphp

@foreach($categoryLabels as $catKey => $catLabel)
    @if(isset($buckets[$catKey]))
        <div style="margin-bottom:24px;">
            <h3 style="font-size:14px; font-weight:600; color:{{ $categoryColors[$catKey] ?? 'var(--text)' }}; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px;">
                {{ $catLabel }}
            </h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:40px;">Order</th>
                        <th>Name</th>
                        <th style="width:80px;">Type</th>
                        <th style="width:140px;">Semantic Key</th>
                        <th style="width:60px;">Active</th>
                        <th style="width:60px;">GLs</th>
                        <th style="width:140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($buckets[$catKey] as $bucket)
                    <tr style="{{ !$bucket->is_active ? 'opacity:0.5;' : '' }}">
                        <td style="text-align:center; color:var(--text-dim);">{{ $bucket->sort_order }}</td>
                        <td>
                            <strong>{{ $bucket->name }}</strong>
                            @if($bucket->summary_formula)
                                <br><span style="font-size:11px; color:var(--text-dim); font-family:monospace;">{{ $bucket->summary_formula }}</span>
                            @endif
                        </td>
                        <td>
                            @if($bucket->is_summary)
                                <span style="font-size:11px; background:var(--surface3); padding:2px 8px; border-radius:4px;">Summary</span>
                            @else
                                <span style="font-size:11px; background:var(--accent-dim); color:var(--accent); padding:2px 8px; border-radius:4px;">Input</span>
                            @endif
                        </td>
                        <td style="font-family:monospace; font-size:12px; color:var(--text-muted);">{{ $bucket->semantic_key ?? '—' }}</td>
                        <td style="text-align:center;">{{ $bucket->is_active ? '✓' : '✗' }}</td>
                        <td style="text-align:center;">{{ $bucket->glAccounts()->count() }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.budget-buckets.edit', $bucket) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.budget-buckets.destroy', $bucket) }}" onsubmit="return confirm('Delete this bucket?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endforeach
@endsection
