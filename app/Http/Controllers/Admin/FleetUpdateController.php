<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgencyResource;
use App\Http\Resources\FleetResource;
use App\Http\Resources\DriverResource;
use App\Models\Agency;
use App\Models\Driver;
use App\Models\Fleet;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Carbon\Carbon;

class FleetUpdateController extends Controller
{
    /**
     * Show the fleet update form.
     */
    public function create(Request $request): InertiaResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();

        return Inertia::render('Admin/FleetUpdate/Create', [
            'areas' => $accessibleAreas,
            'step' => 1,
        ]);
    }

    /**
     * Store fleet data with drivers.
     */
    public function store(Request $request): JsonResource|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            // Step 1: Agency Data
            'agency_id' => 'required|exists:agencies,id',

            // Step 2: Fleet Data
            'license_plate' => 'required|string|unique:fleets,license_plate',
            'year_manufacture' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'keur_number' => 'required|string|max:50',
            'keur_expiry' => 'required|date|after_or_equal:today',
            'keur_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'stnk_expiry' => 'required|date|after_or_equal:today',
            'stnk_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'vehicle_expiry' => 'required|date|after_or_equal:today',
            'is_active' => 'boolean',

            // Step 3: Driver Data (array of drivers)
            'drivers' => 'array|max:2',
            'drivers.*.name' => 'required|string|max:100',
            'drivers.*.age' => 'required|integer|min:18|max:65',
            'drivers.*.sim_expiry' => 'required|date|after_or_equal:today',
            'drivers.*.sim_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Create agency if new (from step 1)
            $agency = Agency::find($validated['agency_id']);

            // Create fleet (from step 2)
            $keurDoc = $request->file('keur_document')->store('documents/keur', 'public');
            $stnkDoc = $request->file('stnk_document')->store('documents/stnk', 'public');

            $fleetData = [
                'agency_id' => $validated['agency_id'],
                'license_plate' => $validated['license_plate'],
                'year_manufacture' => $validated['year_manufacture'],
                'keur_number' => $validated['keur_number'],
                'keur_expiry' => $validated['keur_expiry'],
                'keur_document' => $keurDoc,
                'stnk_expiry' => $validated['stnk_expiry'],
                'stnk_document' => $stnkDoc,
                'vehicle_expiry' => $validated['vehicle_expiry'],
                'is_active' => $validated['is_active'] ?? true,
            ];

            $fleet = Fleet::create($fleetData);

            // Create drivers (from step 3)
            if (!empty($validated['drivers'])) {
                foreach ($request->file('drivers') as $index => $driverFile) {
                    $driverData = $validated['drivers'][$index];
                    $simDoc = $driverFile['sim_document']->store('documents/sim', 'public');

                    $driverData['fleet_id'] = $fleet->id;
                    $driverData['sim_document'] = $simDoc;
                    $driverData['is_active'] = $driverData['is_active'] ?? true;
                    Driver::create($driverData);
                }
            }

            DB::commit();

            // Return complete data for confirmation
            $fleet->load('agency.area', 'drivers');
            return new FleetResource($fleet);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to save fleet data',
                'errors' => ['general' => [$e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Show the edit form for existing fleet.
     */
    public function edit(Request $request, Fleet $fleet): InertiaResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();

        $fleet->load('agency.area', 'drivers');

        return Inertia::render('Admin/FleetUpdate/Edit', [
            'fleet' => new FleetResource($fleet),
            'areas' => $accessibleAreas,
            'step' => 1,
        ]);
    }

    /**
     * Update existing fleet data.
     */
    public function update(Request $request, Fleet $fleet): JsonResource|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            // Fleet Data
            'license_plate' => 'required|string|unique:fleets,license_plate,' . $fleet->id,
            'year_manufacture' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'keur_number' => 'required|string|max:50',
            'keur_expiry' => 'required|date|after_or_equal:today',
            'stnk_expiry' => 'required|date|after_or_equal:today',
            'vehicle_expiry' => 'required|date|after_or_equal:today',
            'is_active' => 'boolean',

            // Driver Data
            'drivers' => 'array|max:2',
            'drivers.*.id' => 'sometimes|exists:drivers,id',
            'drivers.*.name' => 'required|string|max:100',
            'drivers.*.age' => 'required|integer|min:18|max:65',
            'drivers.*.sim_expiry' => 'required|date|after_or_equal:today',
            'drivers.*.is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Update fleet
            $fleet->update([
                'license_plate' => $validated['license_plate'],
                'year_manufacture' => $validated['year_manufacture'],
                'keur_number' => $validated['keur_number'],
                'keur_expiry' => $validated['keur_expiry'],
                'stnk_expiry' => $validated['stnk_expiry'],
                'vehicle_expiry' => $validated['vehicle_expiry'],
                'is_active' => $validated['is_active'] ?? $fleet->is_active,
            ]);

            // Handle drivers update
            $existingDriverIds = $fleet->drivers()->pluck('id')->toArray();
            $newDriverIds = [];

            if (!empty($validated['drivers'])) {
                foreach ($validated['drivers'] as $driverData) {
                    if (isset($driverData['id']) && in_array($driverData['id'], $existingDriverIds)) {
                        // Update existing driver
                        $driver = Driver::find($driverData['id']);
                        if ($driver) {
                            $driver->update([
                                'name' => $driverData['name'],
                                'age' => $driverData['age'],
                                'sim_expiry' => $driverData['sim_expiry'],
                                'is_active' => $driverData['is_active'] ?? $driver->is_active,
                            ]);
                        }
                    } else {
                        // Create new driver
                        $newDriverData = [
                            'fleet_id' => $fleet->id,
                            'name' => $driverData['name'],
                            'age' => $driverData['age'],
                            'sim_expiry' => $driverData['sim_expiry'],
                            'is_active' => $driverData['is_active'] ?? true,
                        ];
                        $newDriver = Driver::create($newDriverData);
                        $newDriverIds[] = $newDriver->id;
                    }
                }
            }

            // Remove drivers not in updated list
            $driversToDelete = array_diff($existingDriverIds, $newDriverIds);
            if (!empty($driversToDelete)) {
                Driver::whereIn('id', $driversToDelete)->delete();
            }

            DB::commit();

            // Return updated data
            $fleet->load('agency.area', 'drivers');
            return new FleetResource($fleet);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update fleet data',
                'errors' => ['general' => [$e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Validate KEUR expiry (minimum 60 days from today).
     */
    private function validateKEURDate($date): bool
    {
        $minExpiryDate = now()->addDays(60);
        return Carbon::parse($date)->gte($minExpiryDate);
    }

    /**
     * Get agencies for dropdown (AJAX).
     */
    public function getAgencies(Request $request): JsonResource
    {
        $user = $request->user();
        $accessibleAreaIds = $user->getAccessibleAreas()->pluck('id')->toArray();

        $agencies = Agency::whereIn('area_id', $accessibleAreaIds)
            ->with('area')
            ->orderBy('name')
            ->get();

        return AgencyResource::collection($agencies);
    }
}
