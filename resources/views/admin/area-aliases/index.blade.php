@extends('layouts.admin')
@section('title', 'Area Aliases')

@section('content')
<div class="page-header">
    <h1>Area Aliases</h1>
    <a href="{{ route('admin.area-aliases.create') }}" class="btn btn-primary">+ Add Alias</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Alias Text</th>
            <th>Maps To</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($aliases as $alias)
        <tr>
            <td><code>{{ $alias->alias_text }}</code></td>
            <td>{{ $alias->area->name }}</td>
            <td class="actions">
                <a href="{{ route('admin.area-aliases.edit', $alias) }}" class="btn btn-sm btn-secondary">Edit</a>
                <form method="POST" action="{{ route('admin.area-aliases.destroy', $alias) }}" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this alias?')">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" style="text-align:center; color:var(--text-dim); padding:24px;">No aliases defined.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
