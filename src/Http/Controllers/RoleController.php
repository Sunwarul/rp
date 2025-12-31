<?php

namespace NuxtIt\RP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NuxtIt\RP\Http\Requests\StoreRoleRequest;
use NuxtIt\RP\Http\Requests\UpdateRoleRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::with('permissions')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(config('rp.items_per_page', 15));

        return view('rp::roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('rp::roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
        ]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('rp.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return view('rp::roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        return view('rp::roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
        ]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    /**
     * Assign permissions to a role.
     */
    public function assignPermissions(Request $request, $id)
    {
        $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::findOrFail($id);
        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        return redirect()->back()
            ->with('success', 'Permissions assigned successfully.');
    }

    /**
     * Remove a permission from a role.
     */
    public function removePermission($roleId, $permissionId)
    {
        $role = Role::findOrFail($roleId);
        $permission = Permission::findOrFail($permissionId);
        $role->revokePermissionTo($permission);

        return redirect()->back()
            ->with('success', 'Permission removed successfully.');
    }
}

