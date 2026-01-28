<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Agency;
use App\Models\Fleet;
use App\Models\Driver;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request): InertiaResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();
        $selectedAreaId = $request->get('area_id') ?? $user->getFirstArea()?->id;

        if ($selectedAreaId && !$user->hasAreaAccess($selectedAreaId)) {
            $selectedAreaId = $user->getFirstArea()?->id;
        }

        // Get dashboard metrics based on selected area
        $metrics = $this->getDashboardMetrics($selectedAreaId);

        return Inertia::render('Admin/Dashboard/Index', [
            'page_setting' => [
                'title' => 'Dashboard',
                'subtitle' => 'Overview and metrics',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                ],
            ],
            'areas' => $accessibleAreas,
            'selectedAreaId' => $selectedAreaId,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Get dashboard metrics for specific area.
     */
    private function getDashboardMetrics(?int $areaId): array
    {
        if (!$areaId) {
            return $this->getEmptyMetrics();
        }

        $area = Area::find($areaId);

        // Get counts for the selected area
        $agency = Agency::where('area_id', $areaId)->get();
        $agencyCount = $agency->count();
        $fleetCount = Fleet::whereHas('agency', function ($query) use ($areaId) {
            $query->where('area_id', $areaId);
        })->count();
        $driverCount = Driver::whereHas('fleet.agency', function ($query) use ($areaId) {
            $query->where('area_id', $areaId);
        })->count();

        // Calculate cylinder allocation (placeholder - can be calculated based on fleet capacity)
        $cylinderAllocation = $agency->map(function ($agency) {
            return $agency->cylinder_count;
        })->sum();

        // Get daily allocation (placeholder - can be calculated from actual operational data)
        $dailyAllocation = $agency->map(function ($agency) {
            return $agency->daily_delivery;
        })->sum();

        return [
            'area' => [
                'id' => $area->id,
                'name' => $area->name,
                'code' => $area->code,
                'formatted_code' => $area->formatted_code,
            ],
            'metrics' => [
                'agency_count' => $agencyCount,
                'fleet_count' => $fleetCount,
                'cylinder_count' => $cylinderAllocation,
                'driver_count' => $driverCount,
                'daily_allocation' => $dailyAllocation,
            ],
            'agencies' => Agency::where('area_id', $areaId)
                ->with([
                    'fleets.drivers',
                    'fleets' => function ($query) {
                        $query->orderBy('license_plate');
                    }
                ])
                ->withCount('fleets')
                ->orderBy('name')
                ->get()
                ->map(function ($agency) {
                    return [
                        'id' => $agency->id,
                        'name' => $agency->name,
                        'address' => $agency->address,
                        'cylinder_count' => $agency->cylinder_count,
                        'daily_delivery' => $agency->daily_delivery,
                        'fleets_count' => $agency->fleets_count,
                        'fleets' => $agency->fleets->map(function ($fleet) {
                            return [
                                'id' => $fleet->id,
                                'license_plate' => $fleet->license_plate,
                                'year_manufacture' => $fleet->year_manufacture,
                                'keur_number' => $fleet->keur_number,
                                'keur_expiry_date' => $fleet->keur_expiry ? $fleet->keur_expiry->format('d/m/Y') : '-',
                                'keur_status' => $fleet->keur_status ? $fleet->keur_status : '-',
                                'stnk_expiry_date' => $fleet->stnk_expiry ? $fleet->stnk_expiry->format('d/m/Y') : '-',
                                'stnk_status' => $fleet->stnk_status ? $fleet->stnk_status : '-',
                                'vehicle_expiry' => $fleet->vehicle_expiry ? $fleet->vehicle_expiry->format('d/m/Y') : '-',
                                'vehicle_age_status' => $fleet->vehicle_age_status ? $fleet->vehicle_age_status : '-',
                                'is_active' => $fleet->is_active,
                                'drivers' => $fleet->drivers->map(function ($driver) {
                                    return [
                                        'id' => $driver->id,
                                        'name' => $driver->name,
                                        'age' => $driver->age,
                                        'sim_expiry_date' => $driver->sim_expiry ? $driver->sim_expiry->format('d/m/Y') : '-',
                                        'sim_status' => $driver->sim_status,
                                        'is_active' => $driver->is_active,
                                    ];
                                }),
                            ];
                        }),
                    ];
                }),
        ];
    }

    /**
     * Get empty metrics when no area is selected.
     */
    private function getEmptyMetrics(): array
    {
        return [
            'area' => null,
            'metrics' => [
                'agency_count' => 0,
                'fleet_count' => 0,
                'cylinder_count' => 0,
                'driver_count' => 0,
                'daily_allocation' => 0,
            ],
            'agencies' => [],
            'latest_fleets' => [],
        ];
    }
}
