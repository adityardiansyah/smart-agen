<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgencyResource;
use App\Models\Agency;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): InertiaResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();

        $agencies = Agency::with(['area', 'fleets'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('area', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($request->area_id, function ($query, $areaId) use ($accessibleAreas) {
                $areaIds = $accessibleAreas->pluck('id');
                if (in_array($areaId, $areaIds->toArray())) {
                    $query->where('area_id', $areaId);
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Agencies/Index', [
            'page_setting' => [
                'title' => 'Agencies',
                'subtitle' => 'Manage agencies',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Agencies', 'href' => route('admin.agencies.index')],
                ],
            ],
            'page_data' => [
                'agencies' => AgencyResource::collection($agencies),
                'filters' => $request->only(['search', 'area_id', 'status']),
                'areas' => $accessibleAreas,
                'stats' => [
                    'active' => Agency::whereIn('area_id', $accessibleAreas->pluck('id'))->where('is_active', true)->count(),
                    'inactive' => Agency::whereIn('area_id', $accessibleAreas->pluck('id'))->where('is_active', false)->count(),
                    'total' => Agency::whereIn('area_id', $accessibleAreas->pluck('id'))->count(),
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

        return Inertia::render('Admin/Agencies/Create', [
            'page_setting' => [
                'title' => 'Tambah Agen',
                'subtitle' => 'Buat agen baru',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Agencies', 'href' => route('admin.agencies.index')],
                    ['title' => 'Create', 'href' => route('admin.agencies.create')],
                ],
            ],
            'areas' => $accessibleAreas,
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
            'area_id' => 'required|exists:areas,id|in:' . $areaIds->implode(','),
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:100',
            'address' => 'required|string',
            'cylinder_count' => 'required|integer|min:0',
            'daily_allocation' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $agency = Agency::create($validated);

        return redirect()->route('admin.agencies.index')
            ->with('success', "Agen \"{$agency->name}\" berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Agency $agency): JsonResource
    {
        $agency->load(['area', 'fleets' => function ($query) {
            $query->withCount('drivers')->orderBy('license_plate');
        }]);

        return new AgencyResource($agency);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Agency $agency): InertiaResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();

        return Inertia::render('Admin/Agencies/Edit', [
            'page_setting' => [
                'title' => 'Edit Agen',
                'subtitle' => 'Edit informasi agen',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Agencies', 'href' => route('admin.agencies.index')],
                    ['title' => 'Edit', 'href' => route('admin.agencies.edit', $agency)],
                ],
            ],
            'agency' => new AgencyResource($agency),
            'areas' => $accessibleAreas,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agency $agency): RedirectResponse
    {
        $user = $request->user();
        $accessibleAreas = $user->getAccessibleAreas();
        $areaIds = $accessibleAreas->pluck('id');

        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id|in:' . $areaIds->implode(','),
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:100',
            'address' => 'required|string',
            'cylinder_count' => 'required|integer|min:0',
            'daily_allocation' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $agency->update($validated);

        return redirect()->route('admin.agencies.index')
            ->with('success', "Agen \"{$agency->name}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agency $agency): RedirectResponse
    {
        // Check if agency has fleets
        if ($agency->fleets()->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus agen. Agen memiliki armada terkait.');
        }

        $agencyName = $agency->name;
        $agency->delete();

        return redirect()->route('admin.agencies.index')
            ->with('success', "Agen \"{$agencyName}\" berhasil dihapus.");
    }

    /**
     * Toggle agency status.
     */
    public function toggleStatus(Agency $agency): RedirectResponse
    {
        $agency->update(['is_active' => !$agency->is_active]);

        return redirect()->back()
            ->with('success', "Status agen \"{$agency->name}\" berhasil diperbarui.");
    }

    /**
     * Get regions for a specific area.
     */
    public function getRegions(Request $request): \Illuminate\Http\JsonResponse
    {
        $areaId = $request->input('area_id');

        $regions = \App\Models\Region::where('area_id', $areaId)
            ->orderBy('city')
            ->get(['id', 'city', 'region_sbm']);

        return response()->json($regions);
    }
}
