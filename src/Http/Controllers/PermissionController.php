<?php

namespace NuxtIt\RP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NuxtIt\RP\Http\Requests\StorePermissionRequest;
use NuxtIt\RP\Http\Requests\UpdatePermissionRequest;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $permissions = Permission::with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(config('rp.items_per_page', 15));

        return view('rp::permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('rp::permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        Permission::create([
            'name' => $request->name,
        ]);

        return redirect()->route('rp.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $permission = Permission::with('roles')->findOrFail($id);
        return view('rp::permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('rp::permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
        ]);

        return redirect()->route('rp.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('rp.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}

