@extends('layouts.admin')
@section('title', 'Add Budget')

@section('content')
<div class="page-header">
    <h1>Add Budget</h1>
    <a href="{{ route('admin.budgets.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.budgets.store') }}">
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

        <h3 style="margin:20px 0 12px; font-size:14px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Revenue Breakdown</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="revenue">Total Revenue ($)</label>
                <input type="number" name="revenue" id="revenue" class="form-control" value="{{ old('revenue') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="individual_donations">Individual Donations ($)</label>
                <input type="number" name="individual_donations" id="individual_donations" class="form-control" value="{{ old('individual_donations') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="church_giving">Church Giving ($)</label>
                <input type="number" name="church_giving" id="church_giving" class="form-control" value="{{ old('church_giving') }}" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="grant_revenue">Grant Revenue ($)</label>
                <input type="number" name="grant_revenue" id="grant_revenue" class="form-control" value="{{ old('grant_revenue') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="foundation_revenue">Foundation Revenue ($)</label>
                <input type="number" name="foundation_revenue" id="foundation_revenue" class="form-control" value="{{ old('foundation_revenue') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="fundraising_events">Fundraising Events ($)</label>
                <input type="number" name="fundraising_events" id="fundraising_events" class="form-control" value="{{ old('fundraising_events') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="institutional">Institutional ($)</label>
                <input type="number" name="institutional" id="institutional" class="form-control" value="{{ old('institutional') }}" step="0.01">
            </div>
        </div>

        <h3 style="margin:20px 0 12px; font-size:14px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Expenses &amp; Profit</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="cogs">COGS ($)</label>
                <input type="number" name="cogs" id="cogs" class="form-control" value="{{ old('cogs') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="gross_profit">Gross Profit ($)</label>
                <input type="number" name="gross_profit" id="gross_profit" class="form-control" value="{{ old('gross_profit') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="program_costs">Program Costs ($)</label>
                <input type="number" name="program_costs" id="program_costs" class="form-control" value="{{ old('program_costs') }}" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="admin_costs">Admin Costs ($)</label>
                <input type="number" name="admin_costs" id="admin_costs" class="form-control" value="{{ old('admin_costs') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="total_expenses">Total Expenses ($)</label>
                <input type="number" name="total_expenses" id="total_expenses" class="form-control" value="{{ old('total_expenses') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="net_operating">Net Operating ($)</label>
                <input type="number" name="net_operating" id="net_operating" class="form-control" value="{{ old('net_operating') }}" step="0.01">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="rev_sharing">Rev Sharing ($)</label>
                <input type="number" name="rev_sharing" id="rev_sharing" class="form-control" value="{{ old('rev_sharing') }}" step="0.01">
            </div>
            <div class="form-group">
                <label for="net_revenue">Net Revenue ($)</label>
                <input type="number" name="net_revenue" id="net_revenue" class="form-control" value="{{ old('net_revenue') }}" step="0.01">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Record</button>
            <a href="{{ route('admin.budgets.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
