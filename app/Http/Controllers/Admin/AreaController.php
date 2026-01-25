<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): InertiaResponse
    {
        $areas = Area::with(['agencies' => function ($query) {
            $query->with(['fleets' => function ($q) {
                $q->with('drivers')->orderBy('license_plate');
            }])->withCount('fleets')->orderBy('name');
        }])
            ->withCount(['agencies'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Areas/Index', [
            'page_setting' => [
                'title' => 'Areas',
                'subtitle' => 'Manage areas',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Areas', 'href' => route('admin.areas.index')],
                ],
            ],
            'page_data' => [
                'areas' => AreaResource::collection($areas),
                'filters' => $request->only(['search', 'status']),
                'stats' => [
                    'active' => Area::where('is_active', true)->count(),
                    'inactive' => Area::where('is_active', false)->count(),
                    'total' => Area::count(),
                ],
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Admin/Areas/Create', [
            'page_setting' => [
                'title' => 'Tambah Area',
                'subtitle' => 'Buat wilayah operasional baru',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Areas', 'href' => route('admin.areas.index')],
                    ['title' => 'Create', 'href' => route('admin.areas.create')],
                ],
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:areas,name',
            'code' => 'required|string|max:10|unique:areas,code|uppercase',
            'is_active' => 'boolean',
        ]);

        $area = Area::create($validated);

        return redirect()->route('admin.areas.index')
            ->with('success', 'Area berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area): InertiaResponse
    {
        $area->load(['agencies' => function ($query) {
            $query->withCount('fleets')->orderBy('name');
        }, 'regions' => function ($query) {
            $query->withCount('agencies')->orderBy('city');
        }]);

        return Inertia::render('Admin/Areas/Show', [
            'page_setting' => [
                'title' => "Detail Area - {$area->name}",
                'subtitle' => 'Kelola region dan lihat detail area',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Areas', 'href' => route('admin.areas.index')],
                    ['title' => $area->name, 'href' => route('admin.areas.show', $area)],
                ],
            ],
            'area' => new AreaResource($area),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area): InertiaResponse
    {
        return Inertia::render('Admin/Areas/Edit', [
            'page_setting' => [
                'title' => "Edit Area - {$area->name}",
                'subtitle' => 'Update area information',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('admin.dashboard.index')],
                    ['title' => 'Areas', 'href' => route('admin.areas.index')],
                    ['title' => $area->name, 'href' => route('admin.areas.edit', $area)],
                ],
            ],
            'area' => AreaResource::make($area),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Area $area): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:areas,name,' . $area->id,
            'code' => 'required|string|max:10|unique:areas,code,' . $area->id . '|uppercase',
            'is_active' => 'boolean',
        ]);

        $area->update($validated);

        return redirect()->route('admin.areas.index')
            ->with('success', 'Area berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area): RedirectResponse
    {
        // Check if area has relationships
        if ($area->agencies()->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus area. Area memiliki agen terkait.');
        }

        if ($area->users()->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus area. Area memiliki user terkait.');
        }

        $areaName = $area->name;
        $area->delete();

        return redirect()->route('admin.areas.index')
            ->with('success', "Area \"{$areaName}\" berhasil dihapus.");
    }

    /**
     * Toggle area status.
     */
    public function toggleStatus(Area $area): RedirectResponse
    {
        $area->update(['is_active' => !$area->is_active]);

        return redirect()->route('admin.areas.index')
            ->with('success', 'Status area berhasil diperbarui.');
    }
}
