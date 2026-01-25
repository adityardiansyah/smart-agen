<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
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
            'city' => $this->city,
            'region_sbm' => $this->region_sbm,
            'agencies_count' => $this->when(isset($this->agencies_count), $this->agencies_count),
            'area' => $this->when($this->relationLoaded('area'), function () {
                return [
                    'id' => $this->area->id,
                    'name' => $this->area->name,
                    'code' => $this->area->code,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
