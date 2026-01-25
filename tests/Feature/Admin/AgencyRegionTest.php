<?php

namespace Tests\Feature\Admin;

use App\Models\Agency;
use App\Models\Area;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgencyRegionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_regions_by_area_id()
    {
        $area = Area::factory()->create();
        $regions = Region::factory()->count(3)->create(['area_id' => $area->id]);
        $otherArea = Area::factory()->create();
        $otherRegion = Region::factory()->create(['area_id' => $otherArea->id]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('admin.agencies.get-regions', ['area_id' => $area->id]));

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonFragment(['id' => $regions[0]->id])
            ->assertJsonMissing(['id' => $otherRegion->id]);
    }

    public function test_agency_requires_region_id_on_create()
    {
        $area = Area::factory()->create();
        $user = User::factory()->create();
        $user->areas()->attach($area->id);

        $response = $this->actingAs($user)->post(route('admin.agencies.store'), [
            'area_id' => $area->id,
            'name' => 'Test Agency',
            'address' => 'Test Address',
            'regency' => 'Test Regency',
            'province' => 'Test Province',
            // region_id missing
        ]);

        $response->assertSessionHasErrors('region_id');
    }

    public function test_can_create_agency_with_region_id()
    {
        $area = Area::factory()->create();
        $region = Region::factory()->create(['area_id' => $area->id]);
        $user = User::factory()->create();
        $user->areas()->attach($area->id);

        $response = $this->actingAs($user)->post(route('admin.agencies.store'), [
            'area_id' => $area->id,
            'region_id' => $region->id,
            'name' => 'Test Agency',
            'address' => 'Test Address',
            'regency' => 'Test Regency',
            'province' => 'Test Province',
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.agencies.index'));
        $this->assertDatabaseHas('agencies', [
            'name' => 'Test Agency',
            'region_id' => $region->id,
        ]);
    }

    public function test_can_update_agency_with_region_id()
    {
        $area = Area::factory()->create();
        $region = Region::factory()->create(['area_id' => $area->id]);
        $agency = Agency::factory()->create(['area_id' => $area->id, 'region_id' => $region->id]);

        $newRegion = Region::factory()->create(['area_id' => $area->id]);
        $user = User::factory()->create();
        $user->areas()->attach($area->id);

        $response = $this->actingAs($user)->put(route('admin.agencies.update', $agency), [
            'area_id' => $area->id,
            'region_id' => $newRegion->id,
            'name' => 'Updated Agency Name',
            'address' => $agency->address,
            'regency' => $agency->regency,
            'province' => $agency->province,
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.agencies.index'));
        $this->assertDatabaseHas('agencies', [
            'id' => $agency->id,
            'name' => 'Updated Agency Name',
            'region_id' => $newRegion->id,
        ]);
    }
}
