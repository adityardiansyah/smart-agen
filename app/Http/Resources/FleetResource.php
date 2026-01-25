<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FleetResource extends JsonResource
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
            'agency_id' => $this->agency_id,
            'license_plate' => $this->license_plate,
            'formatted_license_plate' => $this->formatted_license_plate,
            'year_manufacture' => $this->year_manufacture,
            'keur_number' => $this->keur_number,
            'keur_expiry' => $this->keur_expiry?->format('d-m-Y'),
            'stnk_expiry' => $this->stnk_expiry?->format('d-m-Y'),
            'vehicle_expiry' => $this->vehicle_expiry?->format('d-m-Y'),
            'keur_document' => $this->keur_document,
            'keur_document_url' => $this->keur_document ? asset('storage/' . $this->keur_document) : null,
            'stnk_document' => $this->stnk_document,
            'stnk_document_url' => $this->stnk_document ? asset('storage/' . $this->stnk_document) : null,
            'is_active' => $this->is_active,
            'keur_status' => $this->keur_status,
            'vehicle_age' => $this->vehicle_age,
            'vehicle_age_status' => $this->vehicle_age_status,
            'active_drivers_count' => $this->when(isset($this->active_drivers_count), $this->active_drivers_count),
            'agency' => $this->when($this->relationLoaded('agency'), function () {
                return [
                    'id' => $this->agency->id,
                    'name' => $this->agency->name,
                    'address' => $this->agency->address,
                    'area' => $this->when($this->agency->relationLoaded('area'), function () {
                        return [
                            'id' => $this->agency->area->id,
                            'name' => $this->agency->area->name,
                            'code' => $this->agency->area->code,
                            'formatted_code' => $this->agency->area->formatted_code,
                        ];
                    }),
                ];
            }),
            'region' => $this->when($this->agency->relationLoaded('region'), function () {
                return [
                    'id' => $this->agency->region->id,
                    'city' => $this->agency->region->city,
                    'region_sbm' => $this->agency->region->region_sbm,
                ];
            }),
            'drivers' => $this->when($this->relationLoaded('drivers'), function () {
                return $this->drivers->map(function ($driver) {
                    return [
                        'id' => $driver->id,
                        'name' => $driver->name,
                        'age' => $driver->age,
                        'sim_expiry' => $driver->sim_expiry,
                        'sim_status' => $driver->sim_status,
                        'days_until_sim_expiry' => $driver->days_until_sim_expiry,
                        'is_active' => $driver->is_active,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
