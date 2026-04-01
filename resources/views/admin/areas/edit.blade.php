@extends('layouts.admin')
@section('title', 'Edit Area')

@section('content')
<div class="page-header">
    <h1>Edit Area: {{ $area->name }}</h1>
    <a href="{{ route('admin.areas.index') }}" class="btn btn-secondary">Back to Areas</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.areas.update', $area) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $area->name) }}" required>
            </div>
            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $area->slug) }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $area->sort_order) }}" required min="0">
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="form-check">
                    <input type="checkbox" name="is_statewide" id="is_statewide" value="1" {{ old('is_statewide', $area->is_statewide) ? 'checked' : '' }}>
                    <label for="is_statewide" style="text-transform:none; font-weight:400; font-size:14px;">Statewide area</label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Area</button>
            <a href="{{ route('admin.areas.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
