@extends('layouts.admin')
@section('title', 'Edit Mission Metric')

@section('content')
<div class="page-header">
    <h1>Edit Mission Metric: {{ $mission->area->name ?? 'N/A' }} (FY{{ $mission->fiscal_year }})</h1>
    <a href="{{ route('admin.mission.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.mission.update', $mission) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="area_id">Area</label>
                <select name="area_id" id="area_id" class="form-control" required>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id', $mission->area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="fiscal_year">Fiscal Year</label>
                <input type="number" name="fiscal_year" id="fiscal_year" class="form-control" value="{{ old('fiscal_year', $mission->fiscal_year) }}" required min="2000" max="2100">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="families_served">Families Served</label>
                <input type="number" name="families_served" id="families_served" class="form-control" value="{{ old('families_served', $mission->families_served) }}">
            </div>
            <div class="form-group">
                <label for="individuals_served">Individuals Served</label>
                <input type="number" name="individuals_served" id="individuals_served" class="form-control" value="{{ old('individuals_served', $mission->individuals_served) }}">
            </div>
            <div class="form-group">
                <label for="avg_monthly_families">Avg Monthly Families</label>
                <input type="number" name="avg_monthly_families" id="avg_monthly_families" class="form-control" value="{{ old('avg_monthly_families', $mission->avg_monthly_families) }}" step="0.1">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="hosted_days">Hosted Days</label>
                <input type="number" name="hosted_days" id="hosted_days" class="form-control" value="{{ old('hosted_days', $mission->hosted_days) }}">
            </div>
            <div class="form-group">
                <label for="hosted_nights">Hosted Nights</label>
                <input type="number" name="hosted_nights" id="hosted_nights" class="form-control" value="{{ old('hosted_nights', $mission->hosted_nights) }}">
            </div>
            <div class="form-group">
                <label for="total_hosted">Total Hosted</label>
                <input type="number" name="total_hosted" id="total_hosted" class="form-control" value="{{ old('total_hosted', $mission->total_hosted) }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="total_volunteers">Total Volunteers</label>
                <input type="number" name="total_volunteers" id="total_volunteers" class="form-control" value="{{ old('total_volunteers', $mission->total_volunteers) }}">
            </div>
            <div class="form-group">
                <label for="partner_churches">Partner Churches</label>
                <input type="number" name="partner_churches" id="partner_churches" class="form-control" value="{{ old('partner_churches', $mission->partner_churches) }}">
            </div>
            <div class="form-group">
                <label for="service_hours">Service Hours</label>
                <input type="number" name="service_hours" id="service_hours" class="form-control" value="{{ old('service_hours', $mission->service_hours) }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="intake">Intake</label>
                <input type="number" name="intake" id="intake" class="form-control" value="{{ old('intake', $mission->intake) }}">
            </div>
            <div class="form-group">
                <label for="opened">Opened</label>
                <input type="number" name="opened" id="opened" class="form-control" value="{{ old('opened', $mission->opened) }}">
            </div>
            <div class="form-group">
                <label for="graduations">Graduations</label>
                <input type="number" name="graduations" id="graduations" class="form-control" value="{{ old('graduations', $mission->graduations) }}">
            </div>
            <div class="form-group">
                <label for="total_relationships">Total Relationships</label>
                <input type="number" name="total_relationships" id="total_relationships" class="form-control" value="{{ old('total_relationships', $mission->total_relationships) }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Record</button>
            <a href="{{ route('admin.mission.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
