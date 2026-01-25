<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('super-admin');

        $manager_sumsel = User::updateOrCreate(
            ['email' => 'manager_sumsel@example.com'],
            [
                'name' => 'Manager Sumsel',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $manager_sumsel->assignRole('manager');

        $asmen_sumsel = User::updateOrCreate(
            ['email' => 'asmen_sumsel@example.com'],
            [
                'name' => 'Asmen Sumsel',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $asmen_sumsel->assignRole('asmen');

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        $operator = User::updateOrCreate(
            ['email' => 'operator@example.com'],
            [
                'name' => 'Operator User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $operator->assignRole('operator');
    }
}
