@extends('layouts.admin')
@section('title', 'Edit User — ' . $user->name)

@section('content')
<div class="page-header">
    <h1>Edit User — {{ $user->name }}</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">← Back</a>
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

<form method="POST" action="{{ route('admin.users.update', $user) }}" style="max-width:500px;">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label class="form-label">Email</label>
        <input type="text" class="form-control" value="{{ $user->email }}" disabled
               style="background:var(--surface2); color:var(--text-muted);">
    </div>

    <div class="form-group">
        <label class="form-label">Role</label>
        <select name="role" class="form-control" id="roleSelect"
                onchange="document.getElementById('areaSection').style.display = this.value === 'area_entry' ? 'block' : 'none'">
            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin — Full access to all areas and admin panel</option>
            <option value="area_entry" {{ old('role', $user->role) === 'area_entry' ? 'selected' : '' }}>Area Entry — Can enter data for assigned areas</option>
            <option value="viewer" {{ old('role', $user->role) === 'viewer' ? 'selected' : '' }}>Viewer — Dashboard view only</option>
        </select>
    </div>

    <div class="form-group" id="areaSection"
         style="{{ old('role', $user->role) === 'area_entry' ? '' : 'display:none;' }}">
        <label class="form-label">Assigned Areas</label>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:4px;">
            @foreach($areas as $area)
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; padding:6px 10px; background:var(--surface2); border-radius:6px; font-size:13px;">
                    <input type="checkbox" name="areas[]" value="{{ $area->id }}"
                           {{ in_array($area->id, old('areas', $userAreaIds)) ? 'checked' : '' }}>
                    {{ $area->name }}
                </label>
            @endforeach
        </div>
        <small style="color:var(--text-dim); font-size:12px; margin-top:6px; display:block;">
            Area Entry users can only enter mission data for the areas checked above.
        </small>
    </div>

    <div style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>
@endsection
