@extends('layouts.admin')
@section('title', 'Edit Starting Cash — ' . $area->name)

@section('content')
<div class="page-header">
    <h1>Starting Cash — {{ $area->name }}</h1>
    <a href="{{ route('admin.starting-cash.index') }}" class="btn btn-secondary">← Back</a>
</div>

@if($errors->any())
    <div style="padding:10px 16px; background:#fce4ec; border-radius:6px; color:#c62828; margin-bottom:16px; font-size:13px;">
        <ul style="margin:0; padding-left:16px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.starting-cash.update', $area) }}" style="max-width:500px;">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label class="form-label">Starting Balance ($)</label>
        <input type="number" name="balance" class="form-control" step="0.01"
               value="{{ old('balance', $balance->balance ?? 0) }}" required>
    </div>

    <div class="form-group">
        <label class="form-label">As Of Date</label>
        <input type="date" name="as_of_date" class="form-control"
               value="{{ old('as_of_date', $balance->as_of_date ? $balance->as_of_date->format('Y-m-d') : '') }}" required>
        <small style="color:var(--text-dim); font-size:12px;">
            Typically the last day of the year before your financial data begins (e.g. 2022-12-31).
        </small>
    </div>

    <div class="form-group">
        <label class="form-label">Notes <span style="color:var(--text-dim); font-weight:normal;">(optional)</span></label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $balance->notes ?? '') }}</textarea>
    </div>

    <div style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
@endsection
