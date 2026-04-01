@extends('layouts.admin')
@section('title', 'Edit Highlight KPI')

@section('content')
<div class="page-header">
    <h1>Edit Highlight KPI: {{ $kpi->label }}</h1>
    <a href="{{ route('admin.highlight-kpis.index') }}" class="btn btn-secondary">Back to KPIs</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.highlight-kpis.update', $kpi) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="label">Display Label</label>
                <input type="text" name="label" id="label" class="form-control" value="{{ old('label', $kpi->label) }}" required>
            </div>
            <div class="form-group">
                <label for="key">Data Key</label>
                <input type="text" name="key" id="key" class="form-control" value="{{ old('key', $kpi->key) }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-control" required>
                    @foreach(['mission' => 'Mission', 'cost' => 'Cost', 'fin' => 'Financial'] as $val => $label)
                        <option value="{{ $val }}" {{ old('type', $kpi->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="color_class">Color Class</label>
                <select name="color_class" id="color_class" class="form-control" required>
                    @foreach(['green' => 'Green', 'accent' => 'Blue (Accent)', 'warm' => 'Orange (Warm)', 'rose' => 'Rose', 'blue' => 'Blue', 'a' => 'Bar A', 'b' => 'Bar B', 'c' => 'Bar C', 'd' => 'Bar D'] as $val => $label)
                        <option value="{{ $val }}" {{ old('color_class', $kpi->color_class) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $kpi->sort_order) }}" required min="0">
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="form-check">
                    <input type="checkbox" name="is_decimal" id="is_decimal" value="1" {{ old('is_decimal', $kpi->is_decimal) ? 'checked' : '' }}>
                    <label for="is_decimal" style="text-transform:none; font-weight:400; font-size:14px;">Show decimal values</label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update KPI</button>
            <a href="{{ route('admin.highlight-kpis.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
