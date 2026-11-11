<?php

namespace App\Repositories;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Acceso a las dos tablas de llenado: `container_sensors` (última lectura por
 * compartimento) y `container_fill_readings` (historial, solo de inserción).
 */
class ContainerFillLevelRepository
{
    private const PRUNE_BATCH_SIZE = 5000;

    /**
     * Guarda cada lectura como el estado actual de su compartimento, salvo que
     * el compartimento ya tenga una lectura más reciente. Es un solo upsert
     * de Postgres, así que dos reportes simultáneos no se pisan: gana siempre
     * la lectura con el `measured_at` más nuevo.
     *
     * El llamador garantiza que no hay dos lecturas con el mismo sensor_key
     * (Postgres rechaza un ON CONFLICT que toca la misma fila dos veces).
     *
     * @param  array<int, array{sensor_key: string, material_type_id: ?int, fill_level: float}>  $readings
     * @return array<string> sensor_key de las lecturas que sí actualizaron el estado actual
     */
    public function applyLatest(int $containerId, array $readings, Carbon $measuredAt): array
    {
        $placeholders = [];
        $bindings = [];

        foreach ($readings as $reading) {
            $placeholders[] = '(?, ?, ?, ?, ?)';
            array_push(
                $bindings,
                $containerId,
                $reading['sensor_key'],
                $reading['material_type_id'],
                $reading['fill_level'],
                $measuredAt->format('Y-m-d H:i:s'),
            );
        }

        $rows = DB::select(
            'INSERT INTO container_sensors (container_id, sensor_key, material_type_id, fill_level, updated_at)
             VALUES '.implode(', ', $placeholders).'
             ON CONFLICT (container_id, sensor_key) DO UPDATE SET
                 material_type_id = EXCLUDED.material_type_id,
                 fill_level = EXCLUDED.fill_level,
                 updated_at = EXCLUDED.updated_at
             WHERE container_sensors.updated_at IS NULL
                OR container_sensors.updated_at <= EXCLUDED.updated_at
             RETURNING sensor_key',
            $bindings,
        );

        return array_column($rows, 'sensor_key');
    }

    /**
     * @param  array<int, array{sensor_key: string, material_type_id: ?int, fill_level: float}>  $readings
     */
    public function insertHistory(
        int $containerId,
        array $readings,
        Carbon $measuredAt,
        int $source,
        ?int $scanId,
    ): void {
        $now = now()->format('Y-m-d H:i:s');

        DB::table('container_fill_readings')->insert(array_map(fn (array $reading) => [
            'container_id' => $containerId,
            'sensor_key' => $reading['sensor_key'],
            'material_type_id' => $reading['material_type_id'],
            'fill_level' => $reading['fill_level'],
            'measured_at' => $measuredAt->format('Y-m-d H:i:s'),
            'source' => $source,
            'scan_id' => $scanId,
            'created_at' => $now,
        ], $readings));
    }

    /**
     * Borra el historial recibido antes de $cutoff, por lotes para no tener
     * la tabla bloqueada en un solo DELETE gigante.
     *
     * @return int filas borradas
     */
    public function pruneHistoryBefore(Carbon $cutoff): int
    {
        $total = 0;

        do {
            $deleted = DB::table('container_fill_readings')
                ->whereIn('id', fn ($query) => $query->select('id')
                    ->from('container_fill_readings')
                    ->where('created_at', '<', $cutoff->format('Y-m-d H:i:s'))
                    ->orderBy('id')
                    ->limit(self::PRUNE_BATCH_SIZE))
                ->delete();

            $total += $deleted;
        } while ($deleted > 0);

        return $total;
    }
}
