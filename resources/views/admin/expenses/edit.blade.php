@extends('layouts.admin')
@section('title', 'Edit Expense Summary')

@section('content')
<div class="page-header">
    <h1>Edit Expenses: {{ $expense->area->name ?? 'N/A' }} (FY{{ $expense->fiscal_year }})</h1>
    <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.expenses.update', $expense) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="area_id">Area</label>
                <select name="area_id" id="area_id" class="form-control" required>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id', $expense->area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="fiscal_year">Fiscal Year</label>
                <input type="number" name="fiscal_year" id="fiscal_year" class="form-control" value="{{ old('fiscal_year', $expense->fiscal_year) }}" required min="2000" max="2100">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="program">Program ($)</label>
                <input type="number" name="program" id="program" class="form-control" value="{{ old('program', $expense->program) }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="admin">Admin ($)</label>
                <input type="number" name="admin" id="admin" class="form-control" value="{{ old('admin', $expense->admin) }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="fundraising">Fundraising ($)</label>
                <input type="number" name="fundraising" id="fundraising" class="form-control" value="{{ old('fundraising', $expense->fundraising) }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="total">Total ($)</label>
                <input type="number" name="total" id="total" class="form-control" value="{{ old('total', $expense->total) }}" step="0.01">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Record</button>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
