@extends('layouts.admin')
@section('title', 'Highlight Groups')

@section('content')
<div class="page-header">
    <h1>Highlight Groups</h1>
    <a href="{{ route('admin.highlights.create') }}" class="btn btn-primary">+ Add Group</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Sort</th>
            <th>Title</th>
            <th>Subtitle</th>
            <th>Color</th>
            <th>KPIs</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($groups as $group)
        <tr>
            <td>{{ $group->sort_order }}</td>
            <td><strong>{{ $group->title }}</strong></td>
            <td style="color:var(--text-muted); font-size:12.5px;">{{ $group->subtitle }}</td>
            <td><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:var(--{{ $group->color }});vertical-align:middle;margin-right:4px;"></span> {{ $group->color }}</td>
            <td>
                @foreach($group->kpis as $kpi)
                    <span style="display:inline-block;background:var(--surface2);padding:2px 8px;border-radius:4px;font-size:12px;margin:1px;">{{ $kpi->label }}</span>
                @endforeach
            </td>
            <td class="actions">
                <a href="{{ route('admin.highlights.edit', $group) }}" class="btn btn-secondary btn-sm">Edit</a>
                <form method="POST" action="{{ route('admin.highlights.destroy', $group) }}" onsubmit="return confirm('Delete this highlight group?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center; color:var(--text-dim); padding:24px;">No highlight groups found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
