@extends('layouts.admin')
@section('title', 'Expense Summaries')

@section('content')
<div class="page-header">
    <h1>Expense Summaries</h1>
    <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">+ Add Record</a>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.expenses.index') }}" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
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

<table class="admin-table">
    <thead>
        <tr>
            <th>Area</th>
            <th>FY</th>
            <th class="num">Program</th>
            <th class="num">Admin</th>
            <th class="num">Fundraising</th>
            <th class="num">Total</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $r)
        <tr>
            <td><strong>{{ $r->area->name ?? 'N/A' }}</strong></td>
            <td>{{ $r->fiscal_year }}</td>
            <td class="num">${{ number_format($r->program ?? 0, 0) }}</td>
            <td class="num">${{ number_format($r->admin ?? 0, 0) }}</td>
            <td class="num">${{ number_format($r->fundraising ?? 0, 0) }}</td>
            <td class="num"><strong>${{ number_format($r->total ?? 0, 0) }}</strong></td>
            <td class="actions">
                <a href="{{ route('admin.expenses.edit', $r) }}" class="btn btn-secondary btn-sm">Edit</a>
                <form method="POST" action="{{ route('admin.expenses.destroy', $r) }}" onsubmit="return confirm('Delete this record?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; color:var(--text-dim); padding:24px;">No records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
