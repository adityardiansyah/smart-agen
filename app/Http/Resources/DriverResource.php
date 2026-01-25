<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
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
            'fleet_id' => $this->fleet_id,
            'name' => $this->name,
            'age' => $this->age,
            'sim_expiry' => $this->sim_expiry?->format('Y-m-d'),
            'sim_document' => $this->sim_document,
            'sim_document_url' => $this->sim_document ? asset('storage/' . $this->sim_document) : null,
            'is_active' => $this->is_active,
            'sim_status' => $this->sim_status,
            'days_until_sim_expiry' => $this->days_until_sim_expiry,
            'fleet' => $this->when($this->relationLoaded('fleet'), function () {
                return [
                    'id' => $this->fleet->id,
                    'license_plate' => $this->fleet->license_plate,
                    'formatted_license_plate' => $this->fleet->formatted_license_plate,
                    'year_manufacture' => $this->fleet->year_manufacture,
                    'keur_status' => $this->fleet->keur_status,
                    'vehicle_age_status' => $this->fleet->vehicle_age_status,
                    'agency' => $this->when($this->fleet->relationLoaded('agency'), function () {
                        return [
                            'id' => $this->fleet->agency->id,
                            'name' => $this->fleet->agency->name,
                            'area' => $this->when($this->fleet->agency->relationLoaded('area'), function () {
                                return [
                                    'id' => $this->fleet->agency->area->id,
                                    'name' => $this->fleet->agency->area->name,
                                    'code' => $this->fleet->agency->area->code,
                                    'formatted_code' => $this->fleet->agency->area->formatted_code,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
