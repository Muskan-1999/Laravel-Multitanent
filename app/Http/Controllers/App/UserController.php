<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users=User::with('roles')->get();
        return view('tenants_user.users.index',['users'=>$users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenants_user.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validateData = $request->validate([
                'name'=>'required|string|max:255',
                'email'=>'required|email|max:255|unique:users,email',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'roles' => 'array',
                'roles.*' => 'string',
            ]);
            
            // Create user
            $user = User::create([
                'name' => $validateData['name'],
                'email' => $validateData['email'],
                'password' => Hash::make($validateData['password']),
            ]);
            
            // Create roles if they don't exist and assign them
            if (!empty($validateData['roles'])) {
                foreach ($validateData['roles'] as $roleName) {
                    $role = \Spatie\Permission\Models\Role::firstOrCreate(
                        ['name' => $roleName, 'guard_name' => 'web']
                    );
                }
                $user->syncRoles($validateData['roles']);
            }
            
            session()->flash('success', 'User created successfully!');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating user: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('tenants_user.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $validateData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'password' => 'nullable|confirmed|min:8',
                'roles' => 'array',
                'roles.*' => 'string',
            ]);
            
            // Update user data
            $user->name = $validateData['name'];
            $user->email = $validateData['email'];
            
            // Update password if provided
            if (!empty($validateData['password'])) {
                $user->password = Hash::make($validateData['password']);
            }
            
            $user->save();
            
            // Update roles
            if (!empty($validateData['roles'])) {
                foreach ($validateData['roles'] as $roleName) {
                    $role = \Spatie\Permission\Models\Role::firstOrCreate(
                        ['name' => $roleName, 'guard_name' => 'web']
                    );
                }
                $user->syncRoles($validateData['roles']);
            }
            
            session()->flash('success', 'User updated successfully!');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating user: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            
            session()->flash('success', 'User deleted successfully!');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting user: ' . $e->getMessage());
            return back();
        }
    }
}
