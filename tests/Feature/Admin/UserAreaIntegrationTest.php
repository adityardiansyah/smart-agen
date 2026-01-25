<?php

namespace Tests\Feature\Admin;

use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserAreaIntegrationTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup basic roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Operator']);

        // Setup areas
        Area::create(['name' => 'Lampung', 'code' => 'LG', 'is_active' => true]);
        Area::create(['name' => 'Sumsel', 'code' => 'SS', 'is_active' => true]);
    }

    /**
     * Test creating user with areas.
     */
    public function test_admin_can_create_user_with_areas()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin);

        $areaIds = Area::all()->pluck('id')->toArray();

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => [Role::where('name', 'Operator')->first()->id],
            'areas' => $areaIds,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'test@example.com')->first();
        $this->assertCount(2, $user->areas);
    }

    /**
     * Test area-based access control for Operator.
     */
    public function test_operator_can_only_access_assigned_areas()
    {
        $operator = User::factory()->create();
        $operator->assignRole('Operator');

        $areaLampung = Area::where('code', 'LG')->first();
        $areaSumsel = Area::where('code', 'SS')->first();

        $operator->areas()->attach($areaLampung->id);

        $this->actingAs($operator);

        // 1. Should have access to Lampung
        $this->assertTrue($operator->hasAreaAccess($areaLampung->id));

        // 2. Should NOT have access to Sumsel
        $this->assertFalse($operator->hasAreaAccess($areaSumsel->id));

        // 3. getAccessibleAreas should only return Lampung
        $accessibleAreas = $operator->getAccessibleAreas();
        $this->assertCount(1, $accessibleAreas);
        $this->assertEquals('LG', $accessibleAreas->first()->code);
    }
}
