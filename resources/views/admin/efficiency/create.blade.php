@extends('layouts.admin')
@section('title', 'Add Efficiency Metric')

@section('content')
<div class="page-header">
    <h1>Add Efficiency Metric</h1>
    <a href="{{ route('admin.efficiency.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.efficiency.store') }}">
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
                <label for="cost_per_individual">Cost per Individual</label>
                <input type="number" name="cost_per_individual" id="cost_per_individual" class="form-control" value="{{ old('cost_per_individual') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="cost_per_family">Cost per Family</label>
                <input type="number" name="cost_per_family" id="cost_per_family" class="form-control" value="{{ old('cost_per_family') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="cost_per_hosted">Cost per Hosted</label>
                <input type="number" name="cost_per_hosted" id="cost_per_hosted" class="form-control" value="{{ old('cost_per_hosted') }}" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="cost_per_intake">Cost per Intake</label>
                <input type="number" name="cost_per_intake" id="cost_per_intake" class="form-control" value="{{ old('cost_per_intake') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="cost_per_graduation">Cost per Graduation</label>
                <input type="number" name="cost_per_graduation" id="cost_per_graduation" class="form-control" value="{{ old('cost_per_graduation') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="cost_per_service_hour">Cost per Service Hour</label>
                <input type="number" name="cost_per_service_hour" id="cost_per_service_hour" class="form-control" value="{{ old('cost_per_service_hour') }}" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="program_cost_ratio">Program Cost Ratio</label>
                <input type="number" name="program_cost_ratio" id="program_cost_ratio" class="form-control" value="{{ old('program_cost_ratio') }}" step="0.0001">
            </div>
            <div class="form-group">
                <label for="admin_ratio">Admin Ratio</label>
                <input type="number" name="admin_ratio" id="admin_ratio" class="form-control" value="{{ old('admin_ratio') }}" step="0.0001">
            </div>
            <div class="form-group">
                <label for="fundraising_roi">Fundraising ROI</label>
                <input type="number" name="fundraising_roi" id="fundraising_roi" class="form-control" value="{{ old('fundraising_roi') }}" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="rev_per_volunteer">Rev per Volunteer</label>
                <input type="number" name="rev_per_volunteer" id="rev_per_volunteer" class="form-control" value="{{ old('rev_per_volunteer') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="ind_per_10k_staff">Ind per $10k Staff</label>
                <input type="number" name="ind_per_10k_staff" id="ind_per_10k_staff" class="form-control" value="{{ old('ind_per_10k_staff') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="intake_conversion">Intake Conversion</label>
                <input type="number" name="intake_conversion" id="intake_conversion" class="form-control" value="{{ old('intake_conversion') }}" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="program_cost">Program Cost ($)</label>
                <input type="number" name="program_cost" id="program_cost" class="form-control" value="{{ old('program_cost') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="revenue">Revenue ($)</label>
                <input type="number" name="revenue" id="revenue" class="form-control" value="{{ old('revenue') }}" step="0.01">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Record</button>
            <a href="{{ route('admin.efficiency.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
