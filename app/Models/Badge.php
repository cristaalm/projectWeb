<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $table = 'badge';

    protected $fillable = [
        'name',
        'points_required',
        'points_awared',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
