@extends('layouts.admin')
@section('title', 'Mission Metrics')

@section('content')
<div class="page-header">
    <h1>Mission Metrics</h1>
    <a href="{{ route('admin.mission.create') }}" class="btn btn-primary">+ Add Record</a>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.mission.index') }}" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
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
                <th class="num">Families</th>
                <th class="num">Individuals</th>
                <th class="num">Avg Mo. Families</th>
                <th class="num">Hosted</th>
                <th class="num">Volunteers</th>
                <th class="num">Svc Hours</th>
                <th class="num">Intake</th>
                <th class="num">Graduations</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $r)
            <tr>
                <td><strong>{{ $r->area->name ?? 'N/A' }}</strong></td>
                <td>{{ $r->fiscal_year }}</td>
                <td class="num">{{ number_format($r->families_served ?? 0) }}</td>
                <td class="num">{{ number_format($r->individuals_served ?? 0) }}</td>
                <td class="num">{{ number_format($r->avg_monthly_families ?? 0, 1) }}</td>
                <td class="num">{{ number_format($r->total_hosted ?? 0) }}</td>
                <td class="num">{{ number_format($r->total_volunteers ?? 0) }}</td>
                <td class="num">{{ number_format($r->service_hours ?? 0) }}</td>
                <td class="num">{{ number_format($r->intake ?? 0) }}</td>
                <td class="num">{{ number_format($r->graduations ?? 0) }}</td>
                <td class="actions">
                    <a href="{{ route('admin.mission.edit', $r) }}" class="btn btn-secondary btn-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.mission.destroy', $r) }}" onsubmit="return confirm('Delete this record?')">
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
