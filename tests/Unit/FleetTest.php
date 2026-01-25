<?php

namespace Tests\Unit;

use App\Models\Fleet;
use Carbon\Carbon;
use Tests\TestCase;

class FleetTest extends TestCase
{
    /**
     * Test KEUR status logic.
     */
    public function test_keur_status_logic()
    {
        $fleet = new Fleet();

        // 1. Not Expired (> 60 days)
        $fleet->keur_expiry = now()->addDays(61);
        $this->assertEquals('NOT EXPIRED', $fleet->keur_status);

        // 2. Near Expiry (< 60 days)
        $fleet->keur_expiry = now()->addDays(59);
        $this->assertEquals('NEAR EXPIRY', $fleet->keur_status);

        // 3. Expired (negative days)
        $fleet->keur_expiry = now()->subDay();
        $this->assertEquals('NEAR EXPIRY', $fleet->keur_status); // Logic says < 60 is NEAR EXPIRY
    }

    /**
     * Test Vehicle Age status logic.
     */
    public function test_vehicle_age_status_logic()
    {
        $fleet = new Fleet();
        $currentYear = now()->year;

        // 1. New vehicle (< 9 years)
        $fleet->year_manufacture = $currentYear - 8;
        $this->assertEquals('NOT EXPIRED', $fleet->vehicle_age_status);

        // 2. Old vehicle (>= 9 years)
        $fleet->year_manufacture = $currentYear - 9;
        $this->assertEquals('NEAR EXPIRED', $fleet->vehicle_age_status);

        // 3. Very old vehicle
        $fleet->year_manufacture = $currentYear - 15;
        $this->assertEquals('NEAR EXPIRED', $fleet->vehicle_age_status);
    }
}
