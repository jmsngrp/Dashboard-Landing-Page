@extends('layouts.admin')
@section('title', 'GL Account Mapping')

@section('content')
<div class="page-header">
    <h1>GL Account Mapping</h1>
    <form method="POST" action="{{ route('admin.gl-accounts.auto-map') }}" style="display:inline">
        @csrf
        <button type="submit" class="btn btn-primary" onclick="return confirm('Auto-map GL accounts to P&L line items using known patterns?')">Auto-Map Accounts</button>
    </form>
</div>

<div class="filter-bar">
    <a href="{{ route('admin.gl-accounts.index', ['filter' => 'all', 'type' => $typeFilter]) }}"
       class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-secondary' }}">All</a>
    <a href="{{ route('admin.gl-accounts.index', ['filter' => 'mapped', 'type' => $typeFilter]) }}"
       class="btn btn-sm {{ $filter === 'mapped' ? 'btn-primary' : 'btn-secondary' }}">Mapped</a>
    <a href="{{ route('admin.gl-accounts.index', ['filter' => 'unmapped', 'type' => $typeFilter]) }}"
       class="btn btn-sm {{ $filter === 'unmapped' ? 'btn-primary' : 'btn-secondary' }}">Unmapped</a>

    <span style="color:var(--text-dim); margin:0 4px;">|</span>

    <a href="{{ route('admin.gl-accounts.index', ['filter' => $filter, 'type' => 'all']) }}"
       class="btn btn-sm {{ $typeFilter === 'all' ? 'btn-primary' : 'btn-secondary' }}">All Types</a>
    <a href="{{ route('admin.gl-accounts.index', ['filter' => $filter, 'type' => 'revenue']) }}"
       class="btn btn-sm {{ $typeFilter === 'revenue' ? 'btn-primary' : 'btn-secondary' }}">Revenue</a>
    <a href="{{ route('admin.gl-accounts.index', ['filter' => $filter, 'type' => 'expense']) }}"
       class="btn btn-sm {{ $typeFilter === 'expense' ? 'btn-primary' : 'btn-secondary' }}">Expense</a>
    <a href="{{ route('admin.gl-accounts.index', ['filter' => $filter, 'type' => 'other']) }}"
       class="btn btn-sm {{ $typeFilter === 'other' ? 'btn-primary' : 'btn-secondary' }}">Other</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Account #</th>
            <th>Name</th>
            <th>Type</th>
            <th>Mapped To</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($accounts as $account)
        <tr>
            <td><code>{{ $account->account_number }}</code></td>
            <td>
                @if($account->depth > 0)
                    <span style="padding-left:{{ $account->depth * 20 }}px; color:var(--text-muted);">&#8627;</span>
                @endif
                {{ $account->account_name }}
            </td>
            <td>{{ ucfirst($account->account_type) }}</td>
            <td>
                @if($account->pnlLineItem)
                    <span style="color:var(--green); font-weight:700;">{{ $account->pnlLineItem->label }}</span>
                @else
                    <span style="color:var(--text-dim);">Not mapped</span>
                @endif
            </td>
            <td>
                <a href="{{ route('admin.gl-accounts.edit', $account) }}" class="btn btn-sm btn-secondary">Edit</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center; color:var(--text-dim); padding:24px;">
                No GL accounts found. Import a GL report first.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:12px; font-size:13px; color:var(--text-dim);">
    Showing {{ $accounts->count() }} accounts
</div>
@endsection
