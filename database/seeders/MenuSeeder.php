<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dashboard - visible to all authenticated users
        Menu::updateOrCreate(
            ['name' => 'Dashboard', 'route_name' => 'dashboard'],
            [
                'icon' => 'layout-dashboard',
                'order' => 1,
                'is_active' => true,
            ]
        );

        // Area Section - parent menu
        $area = Menu::updateOrCreate(
            ['name' => 'Data Area'],
            [
                'icon' => 'map',
                'order' => 2,
                'is_active' => true,
                'permission_name' => 'areas.view',
            ]
        );

        // Agency Section - parent menu
        $agency = Menu::updateOrCreate(
            ['name' => 'Data Agen'],
            [
                'icon' => 'building',
                'order' => 3,
                'is_active' => true,
                'permission_name' => 'agencies.view',
            ]
        );

        // Fleet Section - parent menu
        $fleet = Menu::updateOrCreate(
            ['name' => 'Data Armada'],
            [
                'icon' => 'car',
                'order' => 4,
                'is_active' => true,
                'permission_name' => 'fleets.view',
            ]
        );

        // Driver Section - parent menu
        $driver = Menu::updateOrCreate(
            ['name' => 'Data Supir'],
            [
                'icon' => 'person',
                'order' => 5,
                'is_active' => true,
                'permission_name' => 'drivers.view',
            ]
        );

        // Admin Section - parent menu
        $admin = Menu::updateOrCreate(
            ['name' => 'Administration'],
            [
                'icon' => 'shield',
                'order' => 2,
                'is_active' => true,
                'permission_name' => 'user.view',
            ]
        );

        // User Management
        Menu::updateOrCreate(
            ['name' => 'Users', 'route_name' => 'admin.users.index'],
            [
                'icon' => 'users',
                'parent_id' => $admin->id,
                'order' => 1,
                'is_active' => true,
                'permission_name' => 'user.view',
            ]
        );

        // Role Management
        Menu::updateOrCreate(
            ['name' => 'Roles', 'route_name' => 'admin.roles.index'],
            [
                'icon' => 'user-cog',
                'parent_id' => $admin->id,
                'order' => 2,
                'is_active' => true,
                'permission_name' => 'role.view',
            ]
        );

        // Permission Management
        Menu::updateOrCreate(
            ['name' => 'Permissions', 'route_name' => 'admin.permissions.index'],
            [
                'icon' => 'key',
                'parent_id' => $admin->id,
                'order' => 3,
                'is_active' => true,
                'permission_name' => 'permission.view',
            ]
        );

        // Menu Management
        Menu::updateOrCreate(
            ['name' => 'Menus', 'route_name' => 'admin.menus.index'],
            [
                'icon' => 'menu',
                'parent_id' => $admin->id,
                'order' => 4,
                'is_active' => true,
                'permission_name' => 'menu.view',
            ]
        );

        // Route Access
        Menu::updateOrCreate(
            ['name' => 'Route Access', 'route_name' => 'admin.route-accesses.index'],
            [
                'icon' => 'route',
                'parent_id' => $admin->id,
                'order' => 5,
                'is_active' => true,
                'permission_name' => 'route-access.view',
            ]
        );

        // Activity Logs
        Menu::updateOrCreate(
            ['name' => 'Activity Logs', 'route_name' => 'admin.activity-logs.index'],
            [
                'icon' => 'clipboard-list',
                'parent_id' => $admin->id,
                'order' => 6,
                'is_active' => true,
                'permission_name' => 'activity-log.view',
            ]
        );
    }
}
