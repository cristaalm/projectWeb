<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnvironmentalMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'total_users',
        'total_scans',
        'total_valid_scans',
        'total_points_awarded',
        'kg_recycled',
        'co2_saved_kg',
    ];

    protected $casts = [
        'date' => 'date',
        'total_users' => 'integer',
        'total_scans' => 'integer',
        'total_valid_scans' => 'integer',
        'total_points_awarded' => 'integer',
        'kg_recycled' => 'decimal:2',
        'co2_saved_kg' => 'decimal:2',
    ];
}
