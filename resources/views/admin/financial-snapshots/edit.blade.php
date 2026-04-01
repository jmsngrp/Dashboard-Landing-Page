@extends('layouts.admin')
@section('title', 'Edit Financial Snapshot')

@section('content')
<div class="page-header">
    <h1>Edit Snapshot: {{ $financial_snapshot->area->name ?? 'N/A' }} (FY{{ $financial_snapshot->fiscal_year }})</h1>
    <a href="{{ route('admin.financial-snapshots.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.financial-snapshots.update', $financial_snapshot) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="area_id">Area</label>
                <select name="area_id" id="area_id" class="form-control" required>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id', $financial_snapshot->area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="fiscal_year">Fiscal Year</label>
                <input type="number" name="fiscal_year" id="fiscal_year" class="form-control" value="{{ old('fiscal_year', $financial_snapshot->fiscal_year) }}" required min="2000" max="2100">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="equity">Equity ($)</label>
                <input type="number" name="equity" id="equity" class="form-control" value="{{ old('equity', $financial_snapshot->equity) }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="net_assets">Net Assets ($)</label>
                <input type="number" name="net_assets" id="net_assets" class="form-control" value="{{ old('net_assets', $financial_snapshot->net_assets) }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="net_income_bs">Net Income BS ($)</label>
                <input type="number" name="net_income_bs" id="net_income_bs" class="form-control" value="{{ old('net_income_bs', $financial_snapshot->net_income_bs) }}" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="staffing_budget">Staffing Budget ($)</label>
                <input type="number" name="staffing_budget" id="staffing_budget" class="form-control" value="{{ old('staffing_budget', $financial_snapshot->staffing_budget) }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="target_reserve">Target Reserve ($)</label>
                <input type="number" name="target_reserve" id="target_reserve" class="form-control" value="{{ old('target_reserve', $financial_snapshot->target_reserve) }}" step="0.01">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Record</button>
            <a href="{{ route('admin.financial-snapshots.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
