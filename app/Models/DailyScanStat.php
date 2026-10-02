<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Resumen de escaneos de un día cerrado, por contenedor y material. */
class DailyScanStat extends Model
{
    public $timestamps = false;

    protected $table = 'daily_scan_stats';

    protected $fillable = [
        'date',
        'container_id',
        'material_type_id',
        'valid_scans',
        'failed_scans',
        'points_awarded',
    ];

    protected $casts = [
        'date' => 'date',
        'valid_scans' => 'integer',
        'failed_scans' => 'integer',
        'points_awarded' => 'integer',
    ];
}
