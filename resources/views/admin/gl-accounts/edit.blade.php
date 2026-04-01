@extends('layouts.admin')
@section('title', 'Edit GL Account Mapping')

@section('content')
<div class="page-header">
    <h1>Map GL Account</h1>
    <a href="{{ route('admin.gl-accounts.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="admin-card">
    <div class="form-row" style="margin-bottom:20px;">
        <div class="form-group">
            <label>Account Number</label>
            <div style="padding:8px 0; font-size:14px; font-weight:700;">{{ $glAccount->account_number }}</div>
        </div>
        <div class="form-group">
            <label>Account Name</label>
            <div style="padding:8px 0; font-size:14px;">{{ $glAccount->account_name }}</div>
        </div>
        <div class="form-group">
            <label>Type</label>
            <div style="padding:8px 0; font-size:14px;">{{ ucfirst($glAccount->account_type) }}</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.gl-accounts.update', $glAccount) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="pnl_line_item_id">Map to P&amp;L Line Item</label>
            <select name="pnl_line_item_id" id="pnl_line_item_id" class="form-control">
                <option value="">-- Not Mapped --</option>
                @foreach($lineItems as $category => $items)
                    <optgroup label="{{ ucfirst($category) }}">
                        @foreach($items as $item)
                            <option value="{{ $item->id }}"
                                {{ old('pnl_line_item_id', $glAccount->pnl_line_item_id) == $item->id ? 'selected' : '' }}>
                                [{{ $item->sort_order }}] {{ $item->label }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Mapping</button>
            <a href="{{ route('admin.gl-accounts.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
