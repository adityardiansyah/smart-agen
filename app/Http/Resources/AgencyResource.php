<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'area_id' => $this->area_id,
            'name' => $this->name,
            'address' => $this->address,
            'cylinder_count' => $this->cylinder_count,
            'daily_allocation' => $this->daily_allocation,
            'is_active' => $this->is_active,
            'region_id' => $this->region_id,
            'region' => $this->when($this->relationLoaded('region'), function () {
                return [
                    'id' => $this->region->id,
                    'city' => $this->region->city,
                    'region_sbm' => $this->region->region_sbm,
                ];
            }),
            'full_address' => $this->full_address,
            'active_fleets_count' => $this->when(isset($this->active_fleets_count), $this->active_fleets_count),
            'area' => $this->when($this->relationLoaded('area'), function () {
                return [
                    'id' => $this->area->id,
                    'name' => $this->area->name,
                    'code' => $this->area->code,
                    'formatted_code' => $this->area->formatted_code,
                ];
            }),
            'fleets' => $this->when($this->relationLoaded('fleets'), function () {
                return $this->fleets->map(function ($fleet) {
                    return [
                        'id' => $fleet->id,
                        'license_plate' => $fleet->license_plate,
                        'formatted_license_plate' => $fleet->formatted_license_plate,
                        'year_manufacture' => $fleet->year_manufacture,
                        'keur_status' => $fleet->keur_status,
                        'vehicle_age_status' => $fleet->vehicle_age_status,
                        'drivers_count' => $fleet->drivers_count ?? 0,
                        'is_active' => $fleet->is_active,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
