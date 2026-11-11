<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Última lectura de llenado de un compartimento de un contenedor. La tabla
 * no tiene `created_at`, y `updated_at` guarda la hora en que se midió la
 * lectura (no la de recepción). Se escribe solo desde
 * ContainerFillLevelRepository::applyLatest(), con un upsert que descarta
 * las lecturas más viejas que la guardada.
 */
class ContainerSensor extends Model
{
    protected $table = 'container_sensors';

    public $timestamps = false;

    protected $fillable = [
        'container_id',
        'sensor_key',
        'material_type_id',
        'fill_level',
        'updated_at',
    ];

    protected $casts = [
        'fill_level' => 'float',
        'updated_at' => 'datetime',
    ];

    /**
     * Clave del compartimento de una lectura: la que mande el firmware o,
     * si no manda ninguna, el slug del material (un compartimento por
     * material). Se usa igual al validar y al guardar.
     */
    public static function keyFor(?string $sensorKey, string $material): string
    {
        return $sensorKey !== null && $sensorKey !== '' ? $sensorKey : $material;
    }

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialTypes::class);
    }
}
