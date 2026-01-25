<?php

namespace Tests\Feature\Admin;

use App\Models\Agency;
use App\Models\Area;
use App\Models\Driver;
use App\Models\Fleet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentUploadTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    protected $user;
    protected $area;
    protected $agency;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->area = Area::create([
            'name' => 'Test Area',
            'code' => 'TA',
            'is_active' => true,
        ]);
        $this->user->areas()->attach($this->area);
        $this->agency = Agency::create([
            'area_id' => $this->area->id,
            'name' => 'Test Agency',
            'address' => 'Test Address',
            'regency' => 'Test Regency',
            'province' => 'Test Province',
            'is_active' => true,
        ]);
    }

    public function test_can_upload_fleet_documents_on_create()
    {
        $this->actingAs($this->user);

        $keurDoc = UploadedFile::fake()->create('keur.pdf', 500);
        $stnkDoc = UploadedFile::fake()->create('stnk.jpg', 500);

        $response = $this->post(route('admin.fleets.store'), [
            'agency_id' => $this->agency->id,
            'license_plate' => 'B 1234 ABC',
            'year_manufacture' => 2020,
            'keur_number' => 'KEUR123',
            'keur_expiry' => now()->addMonths(6)->toDateString(),
            'stnk_expiry' => now()->addYear()->toDateString(),
            'vehicle_expiry' => now()->addYears(5)->toDateString(),
            'keur_document' => $keurDoc,
            'stnk_document' => $stnkDoc,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.fleets.index'));

        $fleet = Fleet::first();
        $this->assertNotNull($fleet->keur_document);
        $this->assertNotNull($fleet->stnk_document);

        Storage::disk('public')->assertExists($fleet->keur_document);
        Storage::disk('public')->assertExists($fleet->stnk_document);
    }

    public function test_can_upload_driver_documents_on_create()
    {
        $this->actingAs($this->user);

        $fleet = Fleet::create([
            'agency_id' => $this->agency->id,
            'license_plate' => 'B 5678 DEF',
            'year_manufacture' => 2019,
            'keur_number' => 'KEUR456',
            'keur_expiry' => now()->addMonths(5)->toDateString(),
            'stnk_expiry' => now()->addYear()->toDateString(),
            'vehicle_expiry' => now()->addYears(4)->toDateString(),
            'is_active' => true,
        ]);
        $simDoc = UploadedFile::fake()->create('sim.png', 500);

        $response = $this->post(route('admin.drivers.store'), [
            'fleet_id' => $fleet->id,
            'name' => 'John Doe',
            'age' => 30,
            'sim_expiry' => now()->addYears(2)->toDateString(),
            'sim_document' => $simDoc,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.drivers.index'));

        $driver = Driver::first();
        $this->assertNotNull($driver->sim_document);

        Storage::disk('public')->assertExists($driver->sim_document);
    }
}
