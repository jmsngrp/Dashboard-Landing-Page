@extends('layouts.admin')
@section('title', 'Add Highlight KPI')

@section('content')
<div class="page-header">
    <h1>Add Highlight KPI</h1>
    <a href="{{ route('admin.highlight-kpis.index') }}" class="btn btn-secondary">Back to KPIs</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.highlight-kpis.store') }}">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="label">Display Label</label>
                <input type="text" name="label" id="label" class="form-control" value="{{ old('label') }}" required placeholder="e.g. Avg Families / Mo">
            </div>
            <div class="form-group">
                <label for="key">Data Key</label>
                <input type="text" name="key" id="key" class="form-control" value="{{ old('key') }}" required placeholder="e.g. avg_families">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-control" required>
                    <option value="mission" {{ old('type') === 'mission' ? 'selected' : '' }}>Mission</option>
                    <option value="cost" {{ old('type') === 'cost' ? 'selected' : '' }}>Cost</option>
                    <option value="fin" {{ old('type') === 'fin' ? 'selected' : '' }}>Financial</option>
                </select>
            </div>
            <div class="form-group">
                <label for="color_class">Color Class</label>
                <select name="color_class" id="color_class" class="form-control" required>
                    @foreach(['green' => 'Green', 'accent' => 'Blue (Accent)', 'warm' => 'Orange (Warm)', 'rose' => 'Rose', 'blue' => 'Blue', 'a' => 'Bar A', 'b' => 'Bar B', 'c' => 'Bar C', 'd' => 'Bar D'] as $val => $label)
                        <option value="{{ $val }}" {{ old('color_class') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" required min="0">
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="form-check">
                    <input type="checkbox" name="is_decimal" id="is_decimal" value="1" {{ old('is_decimal') ? 'checked' : '' }}>
                    <label for="is_decimal" style="text-transform:none; font-weight:400; font-size:14px;">Show decimal values</label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create KPI</button>
            <a href="{{ route('admin.highlight-kpis.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
