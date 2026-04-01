@extends('layouts.admin')
@section('title', 'Add Highlight Group')

@section('content')
<div class="page-header">
    <h1>Add Highlight Group</h1>
    <a href="{{ route('admin.highlights.index') }}" class="btn btn-secondary">Back to Highlights</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.highlights.store') }}">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g. We're serving more families.">
            </div>
            <div class="form-group">
                <label for="subtitle">Subtitle</label>
                <input type="text" name="subtitle" id="subtitle" class="form-control" value="{{ old('subtitle') }}" placeholder="e.g. Average monthly caseload grew year-over-year.">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="color">Color</label>
                <select name="color" id="color" class="form-control">
                    <option value="green" {{ old('color') === 'green' ? 'selected' : '' }}>Green</option>
                    <option value="accent" {{ old('color') === 'accent' ? 'selected' : '' }}>Blue (Accent)</option>
                    <option value="warm" {{ old('color') === 'warm' ? 'selected' : '' }}>Orange (Warm)</option>
                    <option value="rose" {{ old('color') === 'rose' ? 'selected' : '' }}>Rose</option>
                    <option value="blue" {{ old('color') === 'blue' ? 'selected' : '' }}>Blue</option>
                </select>
            </div>
            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" required min="0">
            </div>
        </div>

        <div class="form-group">
            <label>KPIs (select in desired order, max 4)</label>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:8px; margin-top:6px;">
                @foreach($kpis as $kpi)
                <label class="form-check" style="padding:8px 12px; background:var(--surface2); border-radius:6px; cursor:pointer;">
                    <input type="checkbox" name="kpis[]" value="{{ $kpi->id }}" {{ in_array($kpi->id, old('kpis', [])) ? 'checked' : '' }}>
                    <span>{{ $kpi->label }} <span style="color:var(--text-dim); font-size:12px;">({{ $kpi->type }})</span></span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Group</button>
            <a href="{{ route('admin.highlights.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
