<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Fleet extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'license_plate',
        'year_manufacture',
        'keur_number',
        'keur_expiry',
        'stnk_expiry',
        'vehicle_expiry',
        'keur_document',
        'stnk_document',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'keur_expiry' => 'date',
        'stnk_expiry' => 'date',
        'vehicle_expiry' => 'date',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByArea($query, $areaId)
    {
        return $query->whereHas('agency', function ($q) use ($areaId) {
            $q->where('area_id', $areaId);
        });
    }

    // KEUR Status Logic sesuai PRD
    public function getKeurStatusAttribute(): string
    {
        if (!$this->keur_expiry) {
            return 'NOT EXPIRED';
        }

        $daysUntilExpiry = now()->diffInDays(Carbon::parse($this->keur_expiry), false);

        return $daysUntilExpiry < 60 ? 'NEAR EXPIRY' : 'NOT EXPIRED';
    }

    public function getStnkStatusAttribute(): string
    {
        if (!$this->stnk_expiry) {
            return 'NOT EXPIRED';
        }

        $daysUntilExpiry = now()->diffInDays(Carbon::parse($this->stnk_expiry), false);

        return $daysUntilExpiry < 30 ? 'NEAR EXPIRY' : 'NOT EXPIRED';
    }

    // Vehicle Age Status Logic sesuai PRD
    public function getVehicleAgeStatusAttribute(): string
    {
        if (!$this->year_manufacture) {
            return 'NOT EXPIRED';
        }

        $vehicleAge = now()->year - $this->year_manufacture;

        if ($vehicleAge <= 8) {
            return 'NOT EXPIRED';
        }

        if ($vehicleAge <= 10) {
            return 'NEAR EXPIRY';
        }

        return 'EXPIRED';
    }

    public function getVehicleAgeAttribute(): int
    {
        return now()->year - $this->year_manufacture;
    }

    public function getFormattedLicensePlateAttribute(): string
    {
        return strtoupper($this->license_plate);
    }

    public function getActiveDriversCountAttribute(): int
    {
        return $this->drivers()->active()->count();
    }

    // Scope untuk filtering berdasarkan status
    public function scopeKeurExpiring($query, $days = 60)
    {
        return $query->where('keur_expiry', '<=', now()->addDays($days));
    }

    public function scopeStnkExpiring($query, $days = 30)
    {
        return $query->where('stnk_expiry', '<=', now()->addDays($days));
    }

    public function scopeOldVehicle($query, $years = 9)
    {
        return $query->where('year_manufacture', '<=', now()->subYears($years)->year);
    }
}
