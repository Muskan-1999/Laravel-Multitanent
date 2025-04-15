<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-users', ['only' => ['index']]);
        $this->middleware('permission:create-users', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-users', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-users', ['only' => ['destroy']]);
    }

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
        if (!auth()->user()->hasPermissionTo('create-users')) {
            return redirect()->route('users.index')
                ->with('error', 'Unauthorized. You don\'t have permission to create users. Please talk with your administrator.');
        }
        return view('tenants_user.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('create-users')) {
            return redirect()->route('users.index')
                ->with('error', 'Unauthorized. You don\'t have permission to create users. Please talk with your administrator.');
        }
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
    public function edit(User $user)
    {
        if (!auth()->user()->hasPermissionTo('edit-users')) {
            abort(403, 'Unauthorized action. You only have view permission.');
        }
        return view('tenants_user.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->hasPermissionTo('edit-users')) {
            abort(403, 'Unauthorized action. You only have view permission.');
        }
        try {
            $validateData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
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
    public function destroy(User $user)
    {
        if (!auth()->user()->hasPermissionTo('delete-users')) {
            abort(403, 'Unauthorized action. You only have view permission.');
        }
        try {
            $user->delete();
            
            session()->flash('success', 'User deleted successfully!');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting user: ' . $e->getMessage());
            return back();
        }
    }
}
