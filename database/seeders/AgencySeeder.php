<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Agency;
use App\Models\Area;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = Area::all()->keyBy('code');

        $agencies = [
            // Sumatra Selatan Agencies
            [
                'area_id' => $areas['SS']->id,
                'name' => 'Agen LPG Palembang',
                'address' => 'Jl. Sudirman No. 123',
                'regency' => 'Palembang',
                'province' => 'Sumatra Selatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_id' => $areas['SS']->id,
                'name' => 'Agen LPG Prabumulih',
                'address' => 'Jl. Merdeka No. 456',
                'regency' => 'Prabumulih',
                'province' => 'Sumatra Selatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Lampung Agencies
            [
                'area_id' => $areas['LG']->id,
                'name' => 'Agen LPG Bandar Lampung',
                'address' => 'Jl. Diponegoro No. 789',
                'regency' => 'Bandar Lampung',
                'province' => 'Lampung',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_id' => $areas['LG']->id,
                'name' => 'Agen LPG Metro',
                'address' => 'Jl. Ahmad Yani No. 321',
                'regency' => 'Metro',
                'province' => 'Lampung',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Jambi Agencies
            [
                'area_id' => $areas['JB']->id,
                'name' => 'Agen LPG Jambi',
                'address' => 'Jl. Sutomo No. 654',
                'regency' => 'Jambi',
                'province' => 'Jambi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Bengkulu Agencies
            [
                'area_id' => $areas['BG']->id,
                'name' => 'Agen LPG Bengkulu',
                'address' => 'Jl. Sudirman No. 987',
                'regency' => 'Bengkulu',
                'province' => 'Bengkulu',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Bangka Belitung Agencies
            [
                'area_id' => $areas['BB']->id,
                'name' => 'Agen LPG Pangkal Pinang',
                'address' => 'Jl. P. Diponegoro No. 147',
                'regency' => 'Pangkal Pinang',
                'province' => 'Bangka Belitung',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Agency::insert($agencies);
    }
}