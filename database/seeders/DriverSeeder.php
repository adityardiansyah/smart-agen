<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\Fleet;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fleets = Fleet::all()->keyBy('license_plate');

        $drivers = [
            // BG-1234-SS Drivers
            [
                'fleet_id' => $fleets['BG-1234-SS']->id,
                'name' => 'Ahmad Fauzi',
                'age' => 35,
                'sim_expiry' => now()->addYears(2),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fleet_id' => $fleets['BG-1234-SS']->id,
                'name' => 'Budi Santoso',
                'age' => 28,
                'sim_expiry' => now()->addMonths(8), // Near expiry
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // BG-5678-SS Drivers
            [
                'fleet_id' => $fleets['BG-5678-SS']->id,
                'name' => 'Chandra Wijaya',
                'age' => 42,
                'sim_expiry' => now()->addYears(3),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // BG-9999-SS Driver
            [
                'fleet_id' => $fleets['BG-9999-SS']->id,
                'name' => 'Dedi Kurniawan',
                'age' => 38,
                'sim_expiry' => now()->addMonths(4), // Near expiry
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // BE-1111-LG Drivers
            [
                'fleet_id' => $fleets['BE-1111-LG']->id,
                'name' => 'Eko Prasetyo',
                'age' => 30,
                'sim_expiry' => now()->addYears(4),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fleet_id' => $fleets['BE-1111-LG']->id,
                'name' => 'Faisal Rahman',
                'age' => 33,
                'sim_expiry' => now()->addYears(1),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // BE-2222-LG Driver
            [
                'fleet_id' => $fleets['BE-2222-LG']->id,
                'name' => 'Gunawan Setiawan',
                'age' => 45,
                'sim_expiry' => now()->addMonths(2), // Near expiry
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // BH-3333-JB Drivers
            [
                'fleet_id' => $fleets['BH-3333-JB']->id,
                'name' => 'Hendra Saputra',
                'age' => 36,
                'sim_expiry' => now()->addYears(2),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fleet_id' => $fleets['BH-3333-JB']->id,
                'name' => 'Irwan Susilo',
                'age' => 29,
                'sim_expiry' => now()->addMonths(10),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // BD-4444-BG Driver
            [
                'fleet_id' => $fleets['BD-4444-BG']->id,
                'name' => 'Joko Priyanto',
                'age' => 40,
                'sim_expiry' => now()->addYears(3),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // BN-5555-BB Drivers
            [
                'fleet_id' => $fleets['BN-5555-BB']->id,
                'name' => 'Khalid Ahmad',
                'age' => 32,
                'sim_expiry' => now()->addYears(5),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fleet_id' => $fleets['BN-5555-BB']->id,
                'name' => 'Lukman Hakim',
                'age' => 27,
                'sim_expiry' => now()->addMonths(6), // Near expiry
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Driver::insert($drivers);
    }
}