<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegionResource;
use App\Models\Area;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class RegionController extends Controller
{
    /**
     * Display a listing of the regions for a specific area.
     */
    public function index(Area $area): JsonResource
    {
        $regions = $area->regions()->orderBy('city')->get();

        return RegionResource::collection($regions);
    }

    /**
     * Store a newly created region in storage.
     */
    public function store(Request $request, Area $area): RedirectResponse
    {
        $validated = $request->validate([
            'city' => 'required|string|max:100',
            'region_sbm' => 'required|string|max:100',
        ]);

        $area->regions()->create($validated);

        return redirect()->back()->with('success', 'Region berhasil ditambahkan.');
    }

    /**
     * Update the specified region in storage.
     */
    public function update(Request $request, Region $region): RedirectResponse
    {
        $validated = $request->validate([
            'city' => 'required|string|max:100',
            'region_sbm' => 'required|string|max:100',
        ]);

        $region->update($validated);

        return redirect()->back()->with('success', 'Region berhasil diperbarui.');
    }

    /**
     * Remove the specified region from storage.
     */
    public function destroy(Region $region): RedirectResponse
    {
        // Check if region is used by any agency
        if ($region->agencies()->count() > 0) {
            return redirect()->back()->with('error', 'Region tidak dapat dihapus karena masih digunakan oleh agen.');
        }

        $region->delete();

        return redirect()->back()->with('success', 'Region berhasil dihapus.');
    }
}
