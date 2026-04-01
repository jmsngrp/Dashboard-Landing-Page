@extends('layouts.admin')
@section('title', 'Highlight KPIs')

@section('content')
<div class="page-header">
    <h1>Highlight KPI Pool</h1>
    <a href="{{ route('admin.highlight-kpis.create') }}" class="btn btn-primary">+ Add KPI</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Sort</th>
            <th>Label</th>
            <th>Key</th>
            <th>Type</th>
            <th>Decimal</th>
            <th>Color Class</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kpis as $kpi)
        <tr>
            <td>{{ $kpi->sort_order }}</td>
            <td><strong>{{ $kpi->label }}</strong></td>
            <td><code>{{ $kpi->key }}</code></td>
            <td>{{ $kpi->type }}</td>
            <td>{{ $kpi->is_decimal ? 'Yes' : 'No' }}</td>
            <td><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:var(--{{ $kpi->color_class }});vertical-align:middle;margin-right:4px;"></span> {{ $kpi->color_class }}</td>
            <td class="actions">
                <a href="{{ route('admin.highlight-kpis.edit', $kpi) }}" class="btn btn-secondary btn-sm">Edit</a>
                <form method="POST" action="{{ route('admin.highlight-kpis.destroy', $kpi) }}" onsubmit="return confirm('Delete this KPI?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; color:var(--text-dim); padding:24px;">No KPIs found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
