<?php

namespace App\Models;

use App\Enums\FillReadingSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Una lectura de llenado tal como llegó (historial, solo de inserción).
 * `measured_at` es la hora de la medición; `created_at`, la de recepción.
 */
class ContainerFillReading extends Model
{
    protected $table = 'container_fill_readings';

    public $timestamps = false;

    protected $fillable = [
        'container_id',
        'sensor_key',
        'material_type_id',
        'fill_level',
        'measured_at',
        'source',
        'scan_id',
        'created_at',
    ];

    protected $casts = [
        'fill_level' => 'float',
        'measured_at' => 'datetime',
        'source' => FillReadingSource::class,
        'created_at' => 'datetime',
    ];

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialTypes::class);
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }
}
