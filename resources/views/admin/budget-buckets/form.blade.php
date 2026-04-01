@extends('layouts.admin')
@section('title', $bucket ? 'Edit Bucket' : 'New Bucket')

@section('content')
<div class="page-header">
    <h1>{{ $bucket ? 'Edit Budget Bucket' : 'New Budget Bucket' }}</h1>
    <a href="{{ route('admin.budget-buckets.index') }}" class="btn btn-secondary">← Back</a>
</div>

@if($errors->any())
    <div style="padding:10px 16px; background:#fce4ec; border-radius:6px; color:#c62828; margin-bottom:16px; font-size:13px;">
        <ul style="margin:0; padding-left:16px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ $bucket ? route('admin.budget-buckets.update', $bucket) : route('admin.budget-buckets.store') }}"
      style="max-width:600px;">
    @csrf
    @if($bucket) @method('PUT') @endif

    <div class="form-group">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $bucket->name ?? '') }}" required>
    </div>

    <div class="form-group">
        <label class="form-label">Category</label>
        <select name="category" class="form-control" required>
            @foreach($categories as $key => $label)
                <option value="{{ $key }}" {{ old('category', $bucket->category ?? '') === $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control" min="0"
               value="{{ old('sort_order', $bucket->sort_order ?? $nextSort ?? 0) }}" required>
    </div>

    <div class="form-group" style="display:flex; gap:16px; align-items:center;">
        <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
            <input type="hidden" name="is_summary" value="0">
            <input type="checkbox" name="is_summary" value="1" id="isSummary"
                   {{ old('is_summary', $bucket->is_summary ?? false) ? 'checked' : '' }}
                   onchange="document.getElementById('formulaGroup').style.display = this.checked ? 'block' : 'none'">
            Summary / Computed Row
        </label>

        <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $bucket->is_active ?? true) ? 'checked' : '' }}>
            Active
        </label>
    </div>

    <div class="form-group" id="formulaGroup"
         style="{{ old('is_summary', $bucket->is_summary ?? false) ? '' : 'display:none;' }}">
        <label class="form-label">Summary Formula</label>
        <input type="text" name="summary_formula" class="form-control"
               value="{{ old('summary_formula', $bucket->summary_formula ?? '') }}"
               placeholder="e.g. sum:revenue  or  row:total_income - row:total_opex">
        <small style="color:var(--text-dim); font-size:12px;">
            Use <code>sum:category</code> to sum all non-summary rows in that category,
            or <code>row:key +/- row:key</code> for arithmetic on semantic keys.
        </small>
    </div>

    <div class="form-group">
        <label class="form-label">Semantic Key <span style="color:var(--text-dim); font-weight:normal;">(optional)</span></label>
        <input type="text" name="semantic_key" class="form-control"
               value="{{ old('semantic_key', $bucket->semantic_key ?? '') }}"
               placeholder="e.g. total_income, net_income">
        <small style="color:var(--text-dim); font-size:12px;">
            Used in formulas and dashboard JS. Must be unique. Use snake_case.
        </small>
    </div>

    <div style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">{{ $bucket ? 'Update Bucket' : 'Create Bucket' }}</button>
    </div>
</form>
@endsection
