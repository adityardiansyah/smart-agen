<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'region_id',
        'name',
        'address',
        'cylinder_count',
        'daily_allocation',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'region_id' => 'integer',
        'cylinder_count' => 'integer',
        'daily_allocation' => 'integer',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function fleets(): HasMany
    {
        return $this->hasMany(Fleet::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByArea($query, $areaId)
    {
        return $query->where('area_id', $areaId);
    }

    public function getActiveFleetsCountAttribute(): int
    {
        return $this->fleets()->active()->count();
    }

    public function getFullAddressAttribute(): string
    {
        return $this->address;
    }
}
