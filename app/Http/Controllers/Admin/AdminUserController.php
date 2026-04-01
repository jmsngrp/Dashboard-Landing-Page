<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with('areas')->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $areas = Area::ordered()->get();
        $userAreaIds = $user->areas->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'areas', 'userAreaIds'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role'    => 'required|in:admin,area_entry,viewer',
            'areas'   => 'nullable|array',
            'areas.*' => 'exists:areas,id',
        ]);

        $user->update(['role' => $data['role']]);

        // Sync area assignments (only relevant for area_entry users, but store for all)
        $user->areas()->sync($data['areas'] ?? []);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} updated.");
    }
}
