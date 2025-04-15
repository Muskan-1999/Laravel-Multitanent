<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;


class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = ['view-users', 'edit-users', 'delete-users','create-users'];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

         // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $writer = Role::firstOrCreate(['name' => 'writer', 'guard_name' => 'web']);
        $publisher = Role::firstOrCreate(['name' => 'publisher', 'guard_name' => 'web']);
        $hr = Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
        $manager=Role::firstOrCreate(['name'=>'manager','guard_name'=>'web']);
        $userss=Role::firstOrCreate(['name'=>'user','guard_name'=>'web']);

        // Assign all permissions to admin
        $admin->syncPermissions($permissions);

        
        // Assign specific permissions to other roles as needed
        $writer->syncPermissions(['view-users']);
        $manager->syncPermissions(['view-users', 'edit-users']);
        $hr->syncPermissions(['view-users']);
        $userss->syncPermissions(['view-users']);
        $publisher->syncPermissions(['view-users', 'edit-users']);

    }
}
