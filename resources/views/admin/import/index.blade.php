@extends('layouts.admin')
@section('title', 'CSV Import')

@section('content')
<div class="page-header">
    <h1>CSV Import</h1>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="target">Import Target</label>
                <select name="target" id="target" class="form-control" required>
                    <option value="">Select data type...</option>
                    @foreach($targets as $key => $label)
                        <option value="{{ $key }}" {{ old('target') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="csv_file">CSV File</label>
                <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv,.txt" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Import CSV</button>
        </div>
    </form>
</div>

<div class="admin-card" style="margin-top:24px;">
    <h3 style="font-size:15px; font-weight:700; margin-bottom:16px;">CSV Format Guide</h3>

    <div style="margin-bottom:20px;">
        <h4 style="font-size:13px; font-weight:700; color:var(--text-muted); margin-bottom:6px;">Area Identification</h4>
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:4px;">
            Each row must identify an area using one of: <code>area_slug</code>, <code>area_name</code>, or <code>area_id</code>.
            All rows must also include <code>fiscal_year</code>.
        </p>
    </div>

    <div style="margin-bottom:20px;">
        <h4 style="font-size:13px; font-weight:700; color:var(--text-muted); margin-bottom:6px;">P&amp;L Values</h4>
        <p style="font-size:13px; color:var(--text-muted);">
            Columns: <code>area_slug</code>, <code>fiscal_year</code>, <code>line_item</code> (must match label), <code>amount</code>
        </p>
    </div>

    <div style="margin-bottom:20px;">
        <h4 style="font-size:13px; font-weight:700; color:var(--text-muted); margin-bottom:6px;">Mission Metrics</h4>
        <p style="font-size:13px; color:var(--text-muted);">
            Columns: <code>area_slug</code>, <code>fiscal_year</code>, <code>families_served</code>, <code>individuals_served</code>,
            <code>avg_monthly_families</code>, <code>hosted_days</code>, <code>hosted_nights</code>, <code>total_hosted</code>,
            <code>total_volunteers</code>, <code>partner_churches</code>, <code>service_hours</code>, <code>intake</code>,
            <code>opened</code>, <code>graduations</code>, <code>total_relationships</code>
        </p>
    </div>

    <div style="margin-bottom:20px;">
        <h4 style="font-size:13px; font-weight:700; color:var(--text-muted); margin-bottom:6px;">Revenue Sharing / Local Fundraising</h4>
        <p style="font-size:13px; color:var(--text-muted);">
            Columns: <code>area_slug</code>, <code>fiscal_year</code>, <code>amount</code>
        </p>
    </div>

    <div>
        <h4 style="font-size:13px; font-weight:700; color:var(--text-muted); margin-bottom:6px;">Other Tables</h4>
        <p style="font-size:13px; color:var(--text-muted);">
            Use column names matching the model's fillable fields. Existing records (matched by area + fiscal year) will be updated; new ones will be created.
        </p>
    </div>
</div>
@endsection
