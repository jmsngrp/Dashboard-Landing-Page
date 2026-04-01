@extends('layouts.admin')
@section('title', 'Areas')

@section('content')
<div class="page-header">
    <h1>Areas</h1>
    <a href="{{ route('admin.areas.create') }}" class="btn btn-primary">+ Add Area</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Sort</th>
            <th>Name</th>
            <th>Slug</th>
            <th>Statewide</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($areas as $area)
        <tr>
            <td>{{ $area->sort_order }}</td>
            <td><strong>{{ $area->name }}</strong></td>
            <td><code>{{ $area->slug }}</code></td>
            <td>{{ $area->is_statewide ? 'Yes' : 'No' }}</td>
            <td class="actions">
                <a href="{{ route('admin.areas.edit', $area) }}" class="btn btn-secondary btn-sm">Edit</a>
                <form method="POST" action="{{ route('admin.areas.destroy', $area) }}" onsubmit="return confirm('Delete this area? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center; color:var(--text-dim); padding:24px;">No areas found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
