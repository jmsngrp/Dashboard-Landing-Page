@extends('layouts.admin')
@section('title', 'Bucket Amounts')

@section('content')
<div class="page-header">
    <h1>Bucket Amounts</h1>
</div>

@if(session('success'))
    <div style="padding:10px 16px; background:#e8f5e9; border-radius:6px; color:#2e7d32; margin-bottom:16px; font-size:13px;">
        {{ session('success') }}
    </div>
@endif

<div class="filter-bar" style="margin-bottom:16px;">
    <form method="GET" action="{{ route('admin.bucket-amounts.index') }}" style="display:flex; gap:12px; align-items:center;">
        <label style="font-size:13px; color:var(--text-muted);">Fiscal Year:</label>
        <select name="year" class="form-control" onchange="this.form.submit()">
            @foreach($years as $y)
                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>FY{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

<form method="POST" action="{{ route('admin.bucket-amounts.update') }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="fiscal_year" value="{{ $selectedYear }}">

    <div style="overflow-x:auto;">
        <table class="admin-table" style="font-size:12px;">
            <thead>
                <tr>
                    <th style="position:sticky; left:0; background:var(--surface2); z-index:2; min-width:200px;">Bucket</th>
                    @foreach($areas as $area)
                        <th colspan="2" style="text-align:center; border-bottom:none; min-width:180px;">{{ $area->name }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th style="position:sticky; left:0; background:var(--surface2); z-index:2;"></th>
                    @foreach($areas as $area)
                        <th style="text-align:center; font-size:11px; color:var(--text-dim); font-weight:500;">Budget</th>
                        <th style="text-align:center; font-size:11px; color:var(--text-dim); font-weight:500;">Actual</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($buckets as $bucket)
                <tr style="{{ $bucket->is_summary ? 'background:var(--surface2); font-weight:600;' : '' }}">
                    <td style="position:sticky; left:0; background:{{ $bucket->is_summary ? 'var(--surface3)' : 'var(--surface)' }}; z-index:1;">
                        {{ $bucket->name }}
                        @if($bucket->is_summary)
                            <span style="font-size:10px; color:var(--text-dim); font-weight:normal; display:block; font-family:monospace;">{{ $bucket->summary_formula }}</span>
                        @endif
                    </td>
                    @foreach($areas as $area)
                        @php
                            $record = $lookup[$bucket->id][$area->id] ?? null;
                            $budgetVal = $record ? $record->budget_amount : '';
                            $actualVal = $record ? $record->manual_actual : '';
                            $isGl = $record && $record->source === 'gl_computed';
                        @endphp
                        <td style="padding:2px;">
                            @if($bucket->is_summary)
                                <span style="color:var(--text-dim); font-size:11px; text-align:center; display:block;">
                                    {{ $budgetVal !== '' && $budgetVal !== null ? '$' . number_format((float)$budgetVal, 0) : '—' }}
                                </span>
                            @else
                                <input type="number" step="0.01"
                                       name="amounts[{{ $bucket->id }}][{{ $area->id }}][budget]"
                                       value="{{ $budgetVal }}"
                                       style="width:85px; padding:3px 6px; border:1px solid var(--border); border-radius:4px; font-size:12px; text-align:right;"
                                       placeholder="—">
                            @endif
                        </td>
                        <td style="padding:2px;">
                            @if($bucket->is_summary)
                                <span style="color:var(--text-dim); font-size:11px; text-align:center; display:block;">
                                    {{ $actualVal !== '' && $actualVal !== null ? '$' . number_format((float)$actualVal, 0) : '—' }}
                                </span>
                            @elseif($isGl)
                                <span style="font-size:11px; color:var(--green); text-align:center; display:block;" title="GL-computed">
                                    ${{ number_format((float)$actualVal, 0) }} <small>GL</small>
                                </span>
                            @else
                                <input type="number" step="0.01"
                                       name="amounts[{{ $bucket->id }}][{{ $area->id }}][actual]"
                                       value="{{ $actualVal }}"
                                       style="width:85px; padding:3px 6px; border:1px solid var(--border); border-radius:4px; font-size:12px; text-align:right;"
                                       placeholder="—">
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px; display:flex; gap:12px; align-items:center;">
        <button type="submit" class="btn btn-primary">Save All Changes</button>
        <span style="font-size:12px; color:var(--text-dim);">
            Summary rows and GL-computed actuals are read-only.
        </span>
    </div>
</form>
@endsection
