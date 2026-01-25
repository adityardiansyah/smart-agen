<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaUser extends Model
{
    use HasFactory;

    protected $table = 'area_user';

    protected $fillable = [
        'area_id',
        'user_id',
        'assigned_by',
        'assigned_at',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
