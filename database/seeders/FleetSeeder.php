<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fleet;
use App\Models\Agency;

class FleetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencies = Agency::all()->keyBy('name');

        $fleets = [
            // Palembang Agency Fleets
            [
                'agency_id' => $agencies['Agen LPG Palembang']->id,
                'license_plate' => 'BG-1234-SS',
                'year_manufacture' => 2020,
                'keur_number' => 'KEUR-SS-001',
                'keur_expiry' => now()->addMonths(8),
                'stnk_expiry' => now()->addMonths(6),
                'vehicle_expiry' => now()->addYears(5),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'agency_id' => $agencies['Agen LPG Palembang']->id,
                'license_plate' => 'BG-5678-SS',
                'year_manufacture' => 2018,
                'keur_number' => 'KEUR-SS-002',
                'keur_expiry' => now()->addMonths(2), // Near expiry
                'stnk_expiry' => now()->addMonths(4),
                'vehicle_expiry' => now()->addYears(3),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Prabumulih Agency Fleet
            [
                'agency_id' => $agencies['Agen LPG Prabumulih']->id,
                'license_plate' => 'BG-9999-SS',
                'year_manufacture' => 2015, // Old vehicle (>9 years)
                'keur_number' => 'KEUR-SS-003',
                'keur_expiry' => now()->addMonths(12),
                'stnk_expiry' => now()->addMonths(8),
                'vehicle_expiry' => now()->addYears(2),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Bandar Lampung Agency Fleets
            [
                'agency_id' => $agencies['Agen LPG Bandar Lampung']->id,
                'license_plate' => 'BE-1111-LG',
                'year_manufacture' => 2022,
                'keur_number' => 'KEUR-LG-001',
                'keur_expiry' => now()->addMonths(10),
                'stnk_expiry' => now()->addMonths(12),
                'vehicle_expiry' => now()->addYears(7),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'agency_id' => $agencies['Agen LPG Metro']->id,
                'license_plate' => 'BE-2222-LG',
                'year_manufacture' => 2021,
                'keur_number' => 'KEUR-LG-002',
                'keur_expiry' => now()->addMonths(3), // Near expiry
                'stnk_expiry' => now()->addMonths(5),
                'vehicle_expiry' => now()->addYears(6),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Jambi Agency Fleet
            [
                'agency_id' => $agencies['Agen LPG Jambi']->id,
                'license_plate' => 'BH-3333-JB',
                'year_manufacture' => 2023,
                'keur_number' => 'KEUR-JB-001',
                'keur_expiry' => now()->addMonths(11),
                'stnk_expiry' => now()->addMonths(14),
                'vehicle_expiry' => now()->addYears(8),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Bengkulu Agency Fleet
            [
                'agency_id' => $agencies['Agen LPG Bengkulu']->id,
                'license_plate' => 'BD-4444-BG',
                'year_manufacture' => 2019,
                'keur_number' => 'KEUR-BG-001',
                'keur_expiry' => now()->addMonths(7),
                'stnk_expiry' => now()->addMonths(9),
                'vehicle_expiry' => now()->addYears(4),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Pangkal Pinang Agency Fleet
            [
                'agency_id' => $agencies['Agen LPG Pangkal Pinang']->id,
                'license_plate' => 'BN-5555-BB',
                'year_manufacture' => 2024,
                'keur_number' => 'KEUR-BB-001',
                'keur_expiry' => now()->addMonths(15),
                'stnk_expiry' => now()->addMonths(18),
                'vehicle_expiry' => now()->addYears(10),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Fleet::insert($fleets);
    }
}