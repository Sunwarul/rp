<?php

namespace NuxtIt\RP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use NuxtIt\RP\Http\Requests\StoreUserRequest;
use NuxtIt\RP\Http\Requests\UpdateUserRequest;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = app(config('rp.user_model'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = $this->userModel::with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })
            ->paginate(config('rp.items_per_page', 15));

        return view('rp::users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('rp::users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userModel::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            $user->syncRoles($roles);
        }

        return redirect()->route('rp.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = $this->userModel::with('roles', 'permissions')->findOrFail($id);
        return view('rp::users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = $this->userModel::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('rp::users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userModel::findOrFail($id);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            $user->roles()->sync($roles);
        } else {
            $user->roles()->sync([]);
        }

        return redirect()->route('rp.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = $this->userModel::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('rp.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('rp.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Assign roles to a user.
     */
    public function assignRoles(Request $request, $id)
    {
        $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user = $this->userModel::findOrFail($id);
        $roles = Role::whereIn('id', $request->roles)->get();
        $user->syncRoles($roles);

        return redirect()->back()
            ->with('success', 'Roles assigned successfully.');
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole($userId, $roleId)
    {
        $user = $this->userModel::findOrFail($userId);
        $role = Role::findOrFail($roleId);
        $user->removeRole($role);

        return redirect()->back()
            ->with('success', 'Role removed successfully.');
    }
}

