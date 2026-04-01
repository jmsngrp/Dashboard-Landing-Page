@extends('layouts.admin')
@section('title', 'Efficiency Metrics')

@section('content')
<div class="page-header">
    <h1>Efficiency Metrics</h1>
    <a href="{{ route('admin.efficiency.create') }}" class="btn btn-primary">+ Add Record</a>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.efficiency.index') }}" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
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
                <th class="num">$/Individual</th>
                <th class="num">$/Family</th>
                <th class="num">$/Hosted</th>
                <th class="num">Program Ratio</th>
                <th class="num">Admin Ratio</th>
                <th class="num">Fund. ROI</th>
                <th class="num">Program Cost</th>
                <th class="num">Revenue</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $r)
            <tr>
                <td><strong>{{ $r->area->name ?? 'N/A' }}</strong></td>
                <td>{{ $r->fiscal_year }}</td>
                <td class="num">{{ $r->cost_per_individual !== null ? '$' . number_format($r->cost_per_individual, 0) : '-' }}</td>
                <td class="num">{{ $r->cost_per_family !== null ? '$' . number_format($r->cost_per_family, 0) : '-' }}</td>
                <td class="num">{{ $r->cost_per_hosted !== null ? '$' . number_format($r->cost_per_hosted, 0) : '-' }}</td>
                <td class="num">{{ $r->program_cost_ratio !== null ? number_format($r->program_cost_ratio * 100, 1) . '%' : '-' }}</td>
                <td class="num">{{ $r->admin_ratio !== null ? number_format($r->admin_ratio * 100, 1) . '%' : '-' }}</td>
                <td class="num">{{ $r->fundraising_roi !== null ? number_format($r->fundraising_roi, 2) : '-' }}</td>
                <td class="num">{{ $r->program_cost !== null ? '$' . number_format($r->program_cost, 0) : '-' }}</td>
                <td class="num">{{ $r->revenue !== null ? '$' . number_format($r->revenue, 0) : '-' }}</td>
                <td class="actions">
                    <a href="{{ route('admin.efficiency.edit', $r) }}" class="btn btn-secondary btn-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.efficiency.destroy', $r) }}" onsubmit="return confirm('Delete this record?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align:center; color:var(--text-dim); padding:24px;">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
