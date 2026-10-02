<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Resumen de la actividad de un usuario en un día cerrado. */
class DailyUserStat extends Model
{
    public $timestamps = false;

    protected $table = 'daily_user_stats';

    protected $fillable = [
        'date',
        'user_id',
        'valid_scans',
        'scan_points',
        'badge_points',
    ];

    protected $casts = [
        'date' => 'date',
        'valid_scans' => 'integer',
        'scan_points' => 'integer',
        'badge_points' => 'integer',
    ];
}
