@extends('layouts.admin')
@section('title', 'Edit Area Alias')

@section('content')
<div class="page-header">
    <h1>Edit Area Alias</h1>
    <a href="{{ route('admin.area-aliases.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.area-aliases.update', $areaAlias) }}">
        @csrf
        @method('PUT')
        <div class="form-row">
            <div class="form-group">
                <label for="alias_text">Alias Text</label>
                <input type="text" name="alias_text" id="alias_text" class="form-control"
                       value="{{ old('alias_text', $areaAlias->alias_text) }}" required>
            </div>
            <div class="form-group">
                <label for="area_id">Maps To Area</label>
                <select name="area_id" id="area_id" class="form-control" required>
                    <option value="">Select area...</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}"
                            {{ old('area_id', $areaAlias->area_id) == $area->id ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Alias</button>
            <a href="{{ route('admin.area-aliases.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
