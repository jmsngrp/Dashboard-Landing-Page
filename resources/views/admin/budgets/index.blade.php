@extends('layouts.admin')
@section('title', 'Budgets')

@section('content')
<div class="page-header">
    <h1>Budgets</h1>
    <a href="{{ route('admin.budgets.create') }}" class="btn btn-primary">+ Add Record</a>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.budgets.index') }}" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <select name="year" class="form-control">
            <option value="">All Years</option>
            @foreach($years as $y)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>FY{{ $y }}</option>
            @endforeach
        </select>
        <select name="area_id" class="form-control">
            <option value="">All Areas</option>
            @foreach($areas as $area)
                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </form>
</div>

<div style="overflow-x:auto;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Area</th>
                <th>FY</th>
                <th class="num">Revenue</th>
                <th class="num">COGS</th>
                <th class="num">Gross Profit</th>
                <th class="num">Total Expenses</th>
                <th class="num">Net Operating</th>
                <th class="num">Net Revenue</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $r)
            <tr>
                <td><strong>{{ $r->area->name ?? 'N/A' }}</strong></td>
                <td>{{ $r->fiscal_year }}</td>
                <td class="num">${{ number_format($r->revenue ?? 0, 0) }}</td>
                <td class="num">${{ number_format($r->cogs ?? 0, 0) }}</td>
                <td class="num">${{ number_format($r->gross_profit ?? 0, 0) }}</td>
                <td class="num">${{ number_format($r->total_expenses ?? 0, 0) }}</td>
                <td class="num">${{ number_format($r->net_operating ?? 0, 0) }}</td>
                <td class="num"><strong>${{ number_format($r->net_revenue ?? 0, 0) }}</strong></td>
                <td class="actions">
                    <a href="{{ route('admin.budgets.edit', $r) }}" class="btn btn-secondary btn-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.budgets.destroy', $r) }}" onsubmit="return confirm('Delete this record?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center; color:var(--text-dim); padding:24px;">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
