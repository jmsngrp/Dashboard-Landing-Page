@extends('layouts.admin')
@section('title', 'Add Local Fundraising')

@section('content')
<div class="page-header">
    <h1>Add Local Fundraising</h1>
    <a href="{{ route('admin.local-fundraising.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.local-fundraising.store') }}">
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
            <div class="form-group">
                <label for="amount">Amount ($)</label>
                <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" step="0.01" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Record</button>
            <a href="{{ route('admin.local-fundraising.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
