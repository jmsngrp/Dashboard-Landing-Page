@extends('layouts.admin')
@section('title', 'Add Revenue Source')

@section('content')
<div class="page-header">
    <h1>Add Revenue Source</h1>
    <a href="{{ route('admin.revenue-sources.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.revenue-sources.store') }}">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="area_id">Area</label>
                <select name="area_id" id="area_id" class="form-control" required>
                    <option value="">Select area...</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="fiscal_year">Fiscal Year</label>
                <input type="number" name="fiscal_year" id="fiscal_year" class="form-control" value="{{ old('fiscal_year', date('Y')) }}" required min="2000" max="2100">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="label">Label</label>
                <input type="text" name="label" id="label" class="form-control" value="{{ old('label') }}" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount ($)</label>
                <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Record</button>
            <a href="{{ route('admin.revenue-sources.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
