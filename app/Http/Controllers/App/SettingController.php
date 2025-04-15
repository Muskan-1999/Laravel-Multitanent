<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SettingController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    public function index()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('tenants_user.settings.index', compact('roles', 'permissions'));
    }

    public function updatePermissions(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $role = Role::findOrFail($request->role_id);
        
        // Get all available permissions
        $allPermissions = [
            'view-users',
            'edit-users',
            'delete-users'
        ];

        // Get permissions from request
        $selectedPermissions = $request->permissions ?? [];

        // Sync permissions
        $role->syncPermissions($selectedPermissions);

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully'
        ]);
    }
}
