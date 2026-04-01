@extends('layouts.admin')
@section('title', 'Starting Cash')

@section('content')
<div class="page-header">
    <h1>Starting Cash Balances</h1>
</div>

@if(session('success'))
    <div style="padding:10px 16px; background:#e8f5e9; border-radius:6px; color:#2e7d32; margin-bottom:16px; font-size:13px;">
        {{ session('success') }}
    </div>
@endif

<div style="padding:12px 18px; background:var(--surface2); border-radius:6px; font-size:13px; color:var(--text-muted); margin-bottom:16px;">
    The starting cash balance is the opening balance before your first year of financial data.
    Cash flow is computed as: Starting Balance + Cumulative Net Income per year.
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Area</th>
            <th class="num">Starting Balance</th>
            <th>As Of Date</th>
            <th>Notes</th>
            <th style="width:80px;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($areas as $area)
        @php $scb = $area->startingCashBalance; @endphp
        <tr>
            <td><strong>{{ $area->name }}</strong></td>
            <td class="num">
                @if($scb)
                    ${{ number_format($scb->balance, 2) }}
                @else
                    <span style="color:var(--text-dim);">Not set</span>
                @endif
            </td>
            <td>{{ $scb ? $scb->as_of_date->format('M j, Y') : '—' }}</td>
            <td style="font-size:12px; color:var(--text-muted);">{{ $scb->notes ?? '—' }}</td>
            <td class="actions">
                <a href="{{ route('admin.starting-cash.edit', $area) }}" class="btn btn-secondary btn-sm">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
