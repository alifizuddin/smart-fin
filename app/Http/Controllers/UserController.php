<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department; 
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role; 
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }
    public function create () 
    {
        $this->authorize('create', User::class);
        $roles = Role::all();
        $departments = Department::all();
        return view('admin.users.create', compact('roles', 'departments'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'required|exists:departments,id',
            'roles' => 'required|array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = Role::all();
        $departments = Department::all();
        return view('admin.users.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'required|array',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}