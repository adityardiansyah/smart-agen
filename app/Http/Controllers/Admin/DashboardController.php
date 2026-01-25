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
            'agencies' => Agency::where('area_id', $areaId)->withCount('fleets')->orderBy('name')->take(5)->get(),
            'latest_fleets' => Fleet::whereHas('agency', function ($query) use ($areaId) {
                $query->where('area_id', $areaId);
            })->with(['agency', 'drivers'])->latest()->take(5)->get(),
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
