<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin role (has all permissions by default via Gate::before)
        $superAdmin = Role::updateOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['name' => 'super-admin', 'guard_name' => 'web']
        );

        // Create Admin role with all permissions
        $admin = Role::updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web']
        );
        $manager = Role::updateOrCreate(
            ['name' => 'manager', 'guard_name' => 'web'],
            ['name' => 'manager', 'guard_name' => 'web']
        );
        $asmen = Role::updateOrCreate(
            ['name' => 'asmen', 'guard_name' => 'web'],
            ['name' => 'asmen', 'guard_name' => 'web']
        );

        // Assign all permissions to admin role
        $allPermissions = Permission::all();
        $admin->syncPermissions($allPermissions);
        $manager->syncPermissions($allPermissions);
        $asmen->syncPermissions($allPermissions);
        
        // Create User role with limited permissions
        $operator = Role::updateOrCreate(
            ['name' => 'operator', 'guard_name' => 'web'],
            ['name' => 'operator', 'guard_name' => 'web']
        );
        $operator->syncPermissions([]);

        // Users can only view their own profile (no admin permissions)
    }
}
