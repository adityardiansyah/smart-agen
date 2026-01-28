<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use App\Models\Area;
use App\Models\Fleet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): InertiaResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();

        $drivers = Driver::with(['fleet.agency.area'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('fleet', function ($q) use ($search) {
                        $q->where('license_plate', 'like', "%{$search}%");
                    })
                    ->orWhereHas('fleet.agency', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($request->area_id, function ($query, $areaId) use ($accessibleAreas) {
                $areaIds = $accessibleAreas->pluck('id');
                if (in_array($areaId, $areaIds->toArray())) {
                    $query->whereHas('fleet.agency.area', function ($q) use ($areaId) {
                        $q->where('areas.id', $areaId);
                    });
                }
            })
            ->when($request->sim_status, function ($query, $status) {
                if ($status === 'expired') {
                    $query->simExpired();
                } elseif ($status === 'near_expiry') {
                    $query->simExpiring();
                } elseif ($status === 'not_expired') {
                    $query->where('sim_expiry', '>', now()->addDays(30));
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Drivers/Index', [
            'page_setting' => [
                'title' => 'Drivers',
                'subtitle' => 'Manage drivers',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Drivers', 'href' => route('admin.drivers.index')],
                ],
            ],
            'page_data' => [
                'drivers' => DriverResource::collection($drivers),
                'filters' => $request->only(['search', 'area_id', 'sim_status', 'status']),
                'areas' => $accessibleAreas,
                'stats' => [
                    'sim' => [
                        'not_expired' => Driver::whereHas('fleet.agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('sim_expiry', '>', now()->addDays(30))->count(),
                        // scopeByArea takes query and areaId (single or array? scopeByArea in code was: scopeByArea($query, $areaId) { return $query->whereHas('fleet.agency', function ($q) use ($areaId) { $q->where('area_id', $areaId); }); }
                        // If it expects single ID, I should use whereHas directly for array of IDs.
                        'near_expiry' => Driver::whereHas('fleet.agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('sim_expiry', '<=', now()->addDays(30))->where('sim_expiry', '>', now())->count(),
                        'expired' => Driver::whereHas('fleet.agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->simExpired()->count(),
                    ],
                    'status' => [
                        'active' => Driver::whereHas('fleet.agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('is_active', true)->count(),
                        'inactive' => Driver::whereHas('fleet.agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('is_active', false)->count(),
                        'total' => Driver::whereHas('fleet.agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->count(),
                    ]
                ],
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): InertiaResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();
        $fleets = Fleet::whereHas('agency', function ($q) use ($accessibleAreas) {
            $q->whereIn('area_id', $accessibleAreas->pluck('id'));
        })->orderBy('license_plate')->get();

        return Inertia::render('Admin/Drivers/Create', [
            'page_setting' => [
                'title' => 'Tambah Supir',
                'subtitle' => 'Tambahkan supir baru',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Drivers', 'href' => route('admin.drivers.index')],
                    ['title' => 'Create', 'href' => route('admin.drivers.create')],
                ],
            ],
            'fleets' => $fleets,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fleet_id' => 'required|exists:fleets,id',
            'name' => 'required|string|max:100',
            'age' => 'required|integer|min:18|max:65',
            'sim_expiry' => 'required|date|after_or_equal:today',
            'sim_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('sim_document')) {
            $validated['sim_document'] = $request->file('sim_document')->store('documents/sim', 'public');
        }

        $driver = Driver::create($validated);

        return redirect()->route('admin.drivers.index')
            ->with('success', "Supir \"{$driver->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver): InertiaResponse
    {
        $driver->load(['fleet.agency.area']);

        return Inertia::render('Admin/Drivers/Show', [
            'page_setting' => [
                'title' => "Detail Supir - {$driver->name}",
                'subtitle' => 'Lihat detail informasi supir',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Drivers', 'href' => route('admin.drivers.index')],
                    ['title' => $driver->name, 'href' => route('admin.drivers.show', $driver)],
                ],
            ],
            'driver' => new DriverResource($driver),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Driver $driver): InertiaResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();
        $fleets = Fleet::whereHas('agency', function ($q) use ($accessibleAreas) {
            $q->whereIn('area_id', $accessibleAreas->pluck('id'));
        })->orderBy('license_plate')->get();

        return Inertia::render('Admin/Drivers/Edit', [
            'page_setting' => [
                'title' => 'Edit Supir',
                'subtitle' => 'Edit informasi supir',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Drivers', 'href' => route('admin.drivers.index')],
                    ['title' => 'Edit', 'href' => route('admin.drivers.edit', $driver)],
                ],
            ],
            'driver' => new DriverResource($driver),
            'fleets' => $fleets,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $validated = $request->validate([
            'fleet_id' => 'required|exists:fleets,id',
            'name' => 'required|string|max:100',
            'age' => 'required|integer|min:18|max:65',
            'sim_expiry' => 'required|date|after_or_equal:today',
            'sim_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('sim_document')) {
            $validated['sim_document'] = $request->file('sim_document')->store('documents/sim', 'public');
        }

        $driver->update($validated);

        return redirect()->route('admin.drivers.index')
            ->with('success', "Supir \"{$driver->name}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver): RedirectResponse
    {
        $driverName = $driver->name;
        $driver->delete();

        return redirect()->route('admin.drivers.index')
            ->with('success', "Supir \"{$driverName}\" berhasil dihapus.");
    }

    /**
     * Toggle driver status.
     */
    public function toggleStatus(Driver $driver): RedirectResponse
    {
        $driver->update(['is_active' => !$driver->is_active]);

        return redirect()->back()
            ->with('success', "Status supir \"{$driver->name}\" berhasil diperbarui.");
    }
}
