@extends('layouts.admin')
@section('title', 'Add Area Alias')

@section('content')
<div class="page-header">
    <h1>Add Area Alias</h1>
    <a href="{{ route('admin.area-aliases.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.area-aliases.store') }}">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="alias_text">Alias Text</label>
                <input type="text" name="alias_text" id="alias_text" class="form-control"
                       value="{{ old('alias_text', request('alias_text')) }}" required>
                <p style="font-size:12px; color:var(--text-dim); margin-top:4px;">
                    The text that appears in QBO memo fields (e.g., "NEWI", "Lacrosse")
                </p>
            </div>
            <div class="form-group">
                <label for="area_id">Maps To Area</label>
                <select name="area_id" id="area_id" class="form-control" required>
                    <option value="">Select area...</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Alias</button>
            <a href="{{ route('admin.area-aliases.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
