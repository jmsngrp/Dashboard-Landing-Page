@extends('layouts.admin')
@section('title', 'Add Expense Summary')

@section('content')
<div class="page-header">
    <h1>Add Expense Summary</h1>
    <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.expenses.store') }}">
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
                <label for="program">Program ($)</label>
                <input type="number" name="program" id="program" class="form-control" value="{{ old('program') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="admin">Admin ($)</label>
                <input type="number" name="admin" id="admin" class="form-control" value="{{ old('admin') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="fundraising">Fundraising ($)</label>
                <input type="number" name="fundraising" id="fundraising" class="form-control" value="{{ old('fundraising') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="total">Total ($)</label>
                <input type="number" name="total" id="total" class="form-control" value="{{ old('total') }}" step="0.01">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Record</button>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
