<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Scope to get only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get areas assigned to this user.
     */
    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class)
            ->withPivot('assigned_by', 'assigned_at')
            ->withTimestamps();
    }

    /**
     * Check if user has access to specific area.
     */
    public function hasAreaAccess(int $areaId): bool
    {
        if ($this->isSuperAdmin() || $this->hasRole(['manager', 'asmen', 'admin'])) {
            return true;
        }

        return $this->areas()->where('areas.id', $areaId)->exists();
    }

    /**
     * Get accessible areas for this user.
     */
    public function getAccessibleAreas()
    {
        if ($this->isSuperAdmin() || $this->hasRole(['manager', 'asmen', 'admin'])) {
            return Area::all();
        }

        return $this->areas;
    }

    /**
     * Get first area assigned to user (for default selection).
     */
    public function getFirstArea()
    {
        return $this->areas()->first() ?? Area::first();
    }
}
