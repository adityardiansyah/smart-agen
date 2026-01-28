<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\FleetResource;
use App\Exports\FleetExport;
use App\Models\Agency;
use App\Models\Fleet;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FleetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): InertiaResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();

        $fleets = Fleet::with(['agency.area', 'drivers'])
            ->when($request->search, function ($query, $search) {
                $query->where('license_plate', 'like', "%{$search}%")
                    ->orWhereHas('agency', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($request->area_id, function ($query, $areaId) use ($accessibleAreas) {
                $areaIds = $accessibleAreas->pluck('id');
                if (in_array($areaId, $areaIds->toArray())) {
                    $query->whereHas('agency.area', function ($q) use ($areaId) {
                        $q->where('areas.id', $areaId);
                    });
                }
            })
            ->when($request->keur_status, function ($query, $status) {
                if ($status === 'near_expiry') {
                    $query->keurExpiring();
                } elseif ($status === 'not_expired') {
                    $query->where('keur_expiry', '>', now()->addDays(60));
                } elseif ($status === 'expired') {
                    $query->where('keur_expiry', '<=', now());
                }
            })
            ->when($request->vehicle_age_status, function ($query, $status) {
                if ($status === 'near_expiry') {
                    $query->whereBetween('year_manufacture', [now()->subYears(10)->year, now()->subYears(9)->year]);
                } elseif ($status === 'not_expired') {
                    $query->where('year_manufacture', '>=', now()->subYears(8)->year);
                } elseif ($status === 'expired') {
                    $query->where('year_manufacture', '<', now()->subYears(10)->year);
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('license_plate')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Fleets/Index', [
            'page_setting' => [
                'title' => 'Fleets',
                'subtitle' => 'Manage fleets',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Fleets', 'href' => route('admin.fleets.index')],
                ],
            ],
            'page_data' => [
                'fleets' => FleetResource::collection($fleets),
                'filters' => $request->only(['search', 'area_id', 'keur_status', 'vehicle_age_status', 'status']),
                'areas' => $accessibleAreas,
                'stats' => [
                    'keur' => [
                        'not_expired' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('keur_expiry', '>', now()->addDays(60))->count(),
                        'near_expiry' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->keurExpiring(60)->where('keur_expiry', '>', now())->count(),
                        'expired' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('keur_expiry', '<=', now())->count(),
                    ],
                    'stnk' => [
                        'not_expired' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('stnk_expiry', '>', now()->addDays(30))->count(),
                        'near_expiry' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->stnkExpiring(30)->where('stnk_expiry', '>', now())->count(),
                        'expired' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('stnk_expiry', '<=', now())->count(),
                    ],
                    'vehicle_age' => [
                        'not_expired' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('year_manufacture', '>=', now()->subYears(8)->year)->count(),
                        'near_expiry' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->whereBetween('year_manufacture', [now()->subYears(10)->year, now()->subYears(9)->year])->count(),
                        'expired' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('year_manufacture', '<', now()->subYears(10)->year)->count(),
                    ],
                    'status' => [
                        'active' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('is_active', true)->count(),
                        'inactive' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->where('is_active', false)->count(),
                        'total' => Fleet::whereHas('agency', fn($q) => $q->whereIn('area_id', $accessibleAreas->pluck('id')))->count(),
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
        $agencies = Agency::whereIn('area_id', $accessibleAreas->pluck('id'))->orderBy('name')->get();

        return Inertia::render('Admin/Fleets/Create', [
            'page_setting' => [
                'title' => 'Tambah Armada',
                'subtitle' => 'Tambahkan armada baru',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Fleets', 'href' => route('admin.fleets.index')],
                    ['title' => 'Create', 'href' => route('admin.fleets.create')],
                ],
            ],
            'agencies' => $agencies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();
        $areaIds = $accessibleAreas->pluck('id');

        $validated = $request->validate([
            'agency_id' => 'required|exists:agencies,id',
            'license_plate' => 'required|string|unique:fleets,license_plate',
            'year_manufacture' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'keur_number' => 'required|string|max:50',
            'keur_expiry' => 'required|date|after_or_equal:today',
            'keur_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'stnk_expiry' => 'required|date|after_or_equal:today',
            'stnk_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'vehicle_expiry' => 'required|date|after_or_equal:today',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('keur_document')) {
            $validated['keur_document'] = $request->file('keur_document')->store('documents/keur', 'public');
        }

        if ($request->hasFile('stnk_document')) {
            $validated['stnk_document'] = $request->file('stnk_document')->store('documents/stnk', 'public');
        }

        $fleet = Fleet::create($validated);

        return redirect()->route('admin.fleets.index')
            ->with('success', "Armada \"{$fleet->license_plate}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Fleet $fleet): InertiaResponse
    {
        $fleet->load(['agency.area', 'agency.region', 'drivers' => function ($query) {
            $query->orderByDesc('is_active')->orderByDesc('assigned_at');
        }]);

        // Separate active and historical drivers
        $activeDriver = $fleet->drivers->where('is_active', true)->first();
        $driverHistory = $fleet->drivers->where('is_active', false)->values();

        // Get all inactive drivers from the same area for "existing driver" selection
        $inactiveDrivers = \App\Models\Driver::where('is_active', false)
            ->whereHas('fleet.agency', function ($query) use ($fleet) {
                $query->where('area_id', $fleet->agency->area_id);
            })
            ->whereNull('deactivated_at')
            ->orWhere(function ($query) {
                $query->where('is_active', false)->whereNotNull('deactivated_at');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'fleet_id']);

        return Inertia::render('Admin/Fleets/Show', [
            'page_setting' => [
                'title' => "Detail Armada - {$fleet->license_plate}",
                'subtitle' => 'Lihat detail armada dan histori supir',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Fleets', 'href' => route('admin.fleets.index')],
                    ['title' => $fleet->license_plate, 'href' => route('admin.fleets.show', $fleet)],
                ],
            ],
            'fleet' => new FleetResource($fleet),
            'activeDriver' => $activeDriver ? [
                'id' => $activeDriver->id,
                'name' => $activeDriver->name,
                'age' => $activeDriver->age,
                'sim_expiry' => $activeDriver->sim_expiry?->format('Y-m-d'),
                'sim_status' => $activeDriver->sim_status,
                'sim_document' => $activeDriver->sim_document,
                'sim_document_url' => $activeDriver->sim_document ? asset('storage/' . $activeDriver->sim_document) : null,
                'assigned_at' => $activeDriver->assigned_at?->format('Y-m-d H:i'),
            ] : null,
            'driverHistory' => $driverHistory->map(function ($driver) {
                return [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'age' => $driver->age,
                    'sim_expiry' => $driver->sim_expiry?->format('Y-m-d'),
                    'assigned_at' => $driver->assigned_at?->format('Y-m-d H:i'),
                    'deactivated_at' => $driver->deactivated_at?->format('Y-m-d H:i'),
                ];
            }),
            'inactiveDrivers' => $inactiveDrivers,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Fleet $fleet): InertiaResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();
        $agencies = Agency::whereIn('area_id', $accessibleAreas->pluck('id'))->orderBy('name')->get();

        return Inertia::render('Admin/Fleets/Edit', [
            'page_setting' => [
                'title' => 'Edit Armada',
                'subtitle' => 'Edit informasi armada',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Fleets', 'href' => route('admin.fleets.index')],
                    ['title' => 'Edit', 'href' => route('admin.fleets.edit', $fleet)],
                ],
            ],
            'fleet' => new FleetResource($fleet),
            'agencies' => $agencies,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fleet $fleet): RedirectResponse
    {
        $validated = $request->validate([
            'agency_id' => 'required|exists:agencies,id',
            'license_plate' => 'required|string|unique:fleets,license_plate,' . $fleet->id,
            'year_manufacture' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'keur_number' => 'required|string|max:50',
            'keur_expiry' => 'required|date|after_or_equal:today',
            'keur_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'stnk_expiry' => 'required|date|after_or_equal:today',
            'stnk_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'vehicle_expiry' => 'required|date|after_or_equal:today',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('keur_document')) {
            $validated['keur_document'] = $request->file('keur_document')->store('documents/keur', 'public');
        }

        if ($request->hasFile('stnk_document')) {
            $validated['stnk_document'] = $request->file('stnk_document')->store('documents/stnk', 'public');
        }

        $fleet->update($validated);

        return redirect()->route('admin.fleets.index')
            ->with('success', "Armada \"{$fleet->license_plate}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fleet $fleet): RedirectResponse
    {
        // Check if fleet has drivers
        if ($fleet->drivers()->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus armada. Armada memiliki supir terkait.');
        }

        $licensePlate = $fleet->license_plate;
        $fleet->delete();

        return redirect()->route('admin.fleets.index')
            ->with('success', "Armada \"{$licensePlate}\" berhasil dihapus.");
    }

    /**
     * Toggle fleet status.
     */
    public function toggleStatus(Fleet $fleet): RedirectResponse
    {
        $fleet->update(['is_active' => !$fleet->is_active]);

        return redirect()->back()
            ->with('success', "Status armada \"{$fleet->license_plate}\" berhasil diperbarui.");
    }

    /**
     * Assign a driver to a fleet.
     * If a new driver is added, the current active driver is deactivated.
     */
    public function assignDriver(Request $request, Fleet $fleet): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => 'required|in:new,existing',
            'driver_id' => 'required_if:mode,existing|nullable|exists:drivers,id',
            'name' => 'required_if:mode,new|nullable|string|max:100',
            'age' => 'required_if:mode,new|nullable|integer|min:18|max:65',
            'sim_expiry' => 'required_if:mode,new|nullable|date|after_or_equal:today',
        ]);

        // Deactivate current active driver
        $currentDriver = $fleet->drivers()->where('is_active', true)->first();
        if ($currentDriver) {
            $currentDriver->update([
                'is_active' => false,
                'deactivated_at' => now(),
            ]);
        }

        if ($validated['mode'] === 'new') {
            // Create new driver
            $fleet->drivers()->create([
                'name' => $validated['name'],
                'age' => $validated['age'],
                'sim_expiry' => $validated['sim_expiry'],
                'is_active' => true,
                'assigned_at' => now(),
            ]);
        } else {
            // Re-activate existing driver
            $driver = \App\Models\Driver::find($validated['driver_id']);
            $driver->update([
                'fleet_id' => $fleet->id,
                'is_active' => true,
                'assigned_at' => now(),
                'deactivated_at' => null,
            ]);
        }

        return redirect()->back()
            ->with('success', "Supir berhasil ditugaskan ke armada \"{$fleet->license_plate}\".");
    }

    /**
     * Export fleets to Excel.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $areaId = $request->input('area_id');
        $filename = 'data-armada-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new FleetExport($areaId), $filename);
    }
}
