@extends('layouts.admin')
@section('title', 'User Permissions')

@section('content')
<div class="page-header">
    <h1>User Permissions</h1>
</div>

@if(session('success'))
    <div style="padding:10px 16px; background:#e8f5e9; border-radius:6px; color:#2e7d32; margin-bottom:16px; font-size:13px;">
        {{ session('success') }}
    </div>
@endif

@php
    $roleLabels = [
        'admin'      => ['Admin', '#c62828', '#fce4ec'],
        'area_entry' => ['Area Entry', '#1565c0', '#e3f2fd'],
        'viewer'     => ['Viewer', '#787774', '#f5f5f5'],
    ];
@endphp

<table class="admin-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Assigned Areas</th>
            <th style="width:80px;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        @php $rl = $roleLabels[$user->role] ?? $roleLabels['viewer']; @endphp
        <tr>
            <td><strong>{{ $user->name }}</strong></td>
            <td style="color:var(--text-muted);">{{ $user->email }}</td>
            <td>
                <span style="font-size:11px; padding:2px 10px; border-radius:4px; background:{{ $rl[2] }}; color:{{ $rl[1] }}; font-weight:600;">
                    {{ $rl[0] }}
                </span>
            </td>
            <td style="font-size:12px;">
                @if($user->role === 'admin')
                    <span style="color:var(--text-dim);">All areas (admin)</span>
                @elseif($user->areas->count())
                    {{ $user->areas->pluck('name')->join(', ') }}
                @else
                    <span style="color:var(--text-dim);">None</span>
                @endif
            </td>
            <td class="actions">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-sm">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
