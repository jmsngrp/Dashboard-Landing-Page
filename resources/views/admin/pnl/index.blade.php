@extends('layouts.admin')
@section('title', 'P&L Data')

@section('content')
<div class="page-header">
    <h1>P&amp;L Data</h1>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.pnl.index') }}" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <label style="font-size:13px; font-weight:700; color:var(--text-muted);">Fiscal Year:</label>
        <select name="year" class="form-control" onchange="this.form.submit()">
            @foreach($years as $y)
                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>FY{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

@if($hasGlData)
<div style="background:#ecfdf5; border:1px solid #a7f3d0; color:var(--green); padding:12px 18px; border-radius:6px; margin-bottom:20px; font-size:13.5px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
    <span>GL data is available for FY{{ $selectedYear }}. Values marked with <span style="font-weight:700;">GL</span> were computed from General Ledger transactions.</span>
    <a href="{{ route('admin.gl-accounts.index') }}" class="btn btn-sm btn-secondary">View GL Mappings</a>
</div>
@endif

<div style="overflow-x:auto;">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="min-width:200px;">Line Item</th>
                @foreach($areas as $area)
                    <th class="num" style="min-width:120px;">{{ $area->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($lineItems as $li)
            <tr style="{{ $li->is_total ? 'font-weight:700; background:var(--surface2);' : '' }}">
                <td>{{ $li->label }}</td>
                @foreach($areas as $area)
                    <td class="num">
                        {{ number_format((float)($lookup[$area->id][$li->id] ?? 0), 0) }}
                        @if(($sourceLookup[$area->id][$li->id] ?? 'manual') === 'gl_computed')
                            <span style="font-size:9px; font-weight:700; color:var(--green); vertical-align:super;">GL</span>
                        @endif
                    </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div style="margin-top:20px; padding:12px 18px; background:var(--surface2); border-radius:6px; font-size:13px; color:var(--text-muted);">
    P&amp;L values are computed from GL imports and cannot be edited directly. To update these totals, import updated GL data via <a href="{{ route('admin.gl-import.index') }}" style="color:var(--accent); font-weight:600;">GL Import</a> or adjust account mappings in <a href="{{ route('admin.gl-accounts.index') }}" style="color:var(--accent); font-weight:600;">GL Account Mapping</a>.
</div>
@endsection
