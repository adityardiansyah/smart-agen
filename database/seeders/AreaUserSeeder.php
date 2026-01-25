<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\AreaUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = Area::all()->keyBy('code');
        $user = User::all()->keyBy('email');

        $area_users = [
            [
                'area_id' => $areas['SS']->id,
                'user_id' => $user['manager_sumsel@example.com']->id,
                'assigned_by' => $user['superadmin@example.com']->id,
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_id' => $areas['SS']->id,
                'user_id' => $user['asmen_sumsel@example.com']->id,
                'assigned_by' => $user['manager_sumsel@example.com']->id,
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_id' => $areas['SS']->id,
                'user_id' => $user['operator@example.com']->id,
                'assigned_by' => $user['manager_sumsel@example.com']->id,
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_id' => $areas['SS']->id,
                'user_id' => $user['admin@example.com']->id,
                'assigned_by' => $user['manager_sumsel@example.com']->id,
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];

        foreach ($area_users as $assignment) {
            AreaUser::updateOrCreate(
                [
                    'area_id' => $assignment['area_id'],
                    'user_id' => $assignment['user_id'],
                ],
                $assignment
            );
        }
    }
}
