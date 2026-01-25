<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'fleet_id',
        'name',
        'age',
        'sim_expiry',
        'sim_document',
        'assigned_at',
        'deactivated_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sim_expiry' => 'date',
        'assigned_at' => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    public function fleet(): BelongsTo
    {
        return $this->belongsTo(Fleet::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByArea($query, $areaId)
    {
        return $query->whereHas('fleet.agency', function ($q) use ($areaId) {
            $q->where('area_id', $areaId);
        });
    }

    // SIM Status Logic
    public function getSimStatusAttribute(): string
    {
        if (!$this->sim_expiry) {
            return 'NOT EXPIRED';
        }

        $today = now();
        $expiryDate = Carbon::parse($this->sim_expiry);

        if ($expiryDate->lt($today)) {
            return 'EXPIRED';
        }

        $daysUntilExpiry = $today->diffInDays($expiryDate);

        if ($daysUntilExpiry <= 30) {
            return 'NEAR EXPIRY';
        }

        return 'NOT EXPIRED';
    }

    public function getDaysUntilSimExpiryAttribute(): int
    {
        if (!$this->sim_expiry) {
            return 0;
        }

        return now()->diffInDays(Carbon::parse($this->sim_expiry), false);
    }

    // Scope untuk filtering berdasarkan status SIM
    public function scopeSimExpiring($query, $days = 30)
    {
        return $query->where('sim_expiry', '<=', now()->addDays($days));
    }

    public function scopeSimExpired($query)
    {
        return $query->where('sim_expiry', '<', now());
    }
}
