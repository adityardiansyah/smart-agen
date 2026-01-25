<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agency>
 */
class AgencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $region = \App\Models\Region::factory()->create();

        return [
            'area_id' => $region->area_id,
            'region_id' => $region->id,
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'regency' => $region->city,
            'province' => $this->faker->state(),
            'is_active' => true,
        ];
    }
}
