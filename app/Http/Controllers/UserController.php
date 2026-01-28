<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store()
    {
        return redirect()->back();
    }

    public function edit()
    {
        return view('users.edit');
    }

    public function update()
    {
        return redirect()->back();
    }

    public function destroy()
    {
        return redirect()->back();
    }

    // Show form to edit roles for a user
    public function editRoles(User $user)
    {
        $this->authorizeRoleManagement();

        $roles = \App\Models\Role::all();
        // Use table-prefixed column to avoid ambiguous `id` when joining pivot table
        $userRoles = $user->roles()->pluck('roles.id')->toArray();
        return view('users.edit_roles', compact('user', 'roles', 'userRoles'));
    }

    // Update user roles
    public function updateRoles(Request $request, User $user)
    {
        $this->authorizeRoleManagement();

        $data = $request->validate([
            'roles' => 'array'
        ]);

        $user->roles()->sync($data['roles'] ?? []);

        return redirect()->route('users.index')->with('success', 'تم تحديث أدوار المستخدم');
    }

    protected function authorizeRoleManagement()
    {
        $user = auth()->user();
        if (! $user || (! $user->hasRole('admin') && ! $user->hasPermission('manage_users') && ! $user->hasPermission('manage_settings'))) {
            abort(403);
        }
    }
}
