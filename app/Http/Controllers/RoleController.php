<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware(function ($request, $next) {
        //     $user = Auth::user();
        //     if (! $user || (! $user->hasRole('admin') && ! $user->hasPermission('manage_settings') && ! $user->hasPermission('manage_users'))) {
        //         abort(403);
        //     }
        //     return $next($request);
        // });
    }

    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('settings.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('settings.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
            'permissions' => 'array',
        ]);

        $role = Role::create($data);
        if (!empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return redirect()->route('roles.index')->with('success', 'تم إنشاء الدور بنجاح');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions()->pluck('permissions.id')->toArray();
        return view('settings.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
            'permissions' => 'array',
        ]);

        $role->update($data);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'تم تحديث الدور بنجاح');
    }

    public function destroy(Role $role)
    {
        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'تم حذف الدور');
    }
}
