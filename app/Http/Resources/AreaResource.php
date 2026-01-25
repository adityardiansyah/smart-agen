<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'formatted_code' => $this->formatted_code,
            'is_active' => $this->is_active,
            'agencies_count' => $this->when(isset($this->agencies_count), $this->agencies_count),
            'agencies' => $this->when($this->relationLoaded('agencies'), function () {
                return $this->agencies->map(function ($agency) {
                    return [
                        'id' => $agency->id,
                        'name' => $agency->name,
                        'regency' => $agency->regency,
                        'province' => $agency->province,
                        'fleets_count' => $agency->active_fleets_count ?? 0,
                        'fleets' => $agency->relationLoaded('fleets') ? $agency->fleets->map(function ($fleet) {
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
                                'drivers' => $fleet->relationLoaded('drivers') ? $fleet->drivers->map(function ($driver) {
                                    return [
                                        'id' => $driver->id,
                                        'name' => $driver->name,
                                        'age' => $driver->age,
                                        'sim_expiry_date' => $driver->sim_expiry ? $driver->sim_expiry->format('d/m/Y') : '-',
                                        'sim_status' => $driver->sim_status,
                                        'is_active' => $driver->is_active,
                                    ];
                                }) : [],
                            ];
                        }) : [],
                    ];
                });
            }),
            'regions' => $this->when($this->relationLoaded('regions'), function () {
                return $this->regions->map(function ($region) {
                    return [
                        'id' => $region->id,
                        'city' => $region->city,
                        'region_sbm' => $region->region_sbm,
                        'agencies_count' => $region->agencies_count ?? 0,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
