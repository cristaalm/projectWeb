<?php

namespace App\Services;

use App\Enums\FillReadingSource;
use App\Models\Container;
use App\Models\ContainerSensor;
use App\Repositories\ContainerFillLevelRepository;
use App\Repositories\MaterialTypeRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Lecturas de llenado que reporta un contenedor físico, ya sea dentro de un
 * escaneo o con el reporte periódico. Cada lectura queda en el historial y,
 * si es la más reciente de su compartimento, pasa a ser su estado actual.
 */
class ContainerFillLevelService
{
    public function __construct(
        private readonly ContainerFillLevelRepository $fillLevels,
        private readonly MaterialTypeRepository $materials,
    ) {}

    /**
     * Las lecturas ya vienen validadas (FormRequest): material existente,
     * fill_level entre 0 y 100 y sin compartimentos repetidos.
     *
     * @param  array<int, array{material: string, fill_level: float|int|string, sensor_key?: ?string}>  $readings
     * @return array<int, array{sensor_key: string, material: string, fill_level: float, applied: bool}>
     */
    public function record(
        Container $container,
        array $readings,
        FillReadingSource $source,
        ?Carbon $measuredAt = null,
        ?int $scanId = null,
    ): array {
        // Las columnas son timestamp sin zona: se guardan en la zona de la app,
        // igual que los timestamps que escribe Eloquent.
        $measuredAt = ($measuredAt ?? now())->copy()->setTimezone(config('app.timezone'));

        $materialIds = $this->materials->idsBySlugs(array_column($readings, 'material'));

        $rows = array_map(fn (array $reading) => [
            'sensor_key' => ContainerSensor::keyFor($reading['sensor_key'] ?? null, $reading['material']),
            'material' => $reading['material'],
            'material_type_id' => $materialIds[$reading['material']] ?? null,
            'fill_level' => round((float) $reading['fill_level'], 2),
        ], array_values($readings));

        $applied = DB::transaction(function () use ($container, $rows, $measuredAt, $source, $scanId) {
            $this->fillLevels->insertHistory($container->id, $rows, $measuredAt, $source->value, $scanId);

            return $this->fillLevels->applyLatest($container->id, $rows, $measuredAt);
        });

        return array_map(fn (array $row) => [
            'sensor_key' => $row['sensor_key'],
            'material' => $row['material'],
            'fill_level' => $row['fill_level'],
            'applied' => in_array($row['sensor_key'], $applied, true),
        ], $rows);
    }

    /**
     * Purga el historial más viejo que el plazo de retención configurado
     * (config/containers.php).
     *
     * @return array{cutoff: Carbon, deleted: int}
     */
    public function pruneHistory(): array
    {
        $months = config('containers.fill_readings_retention_months');
        $cutoff = now()->subMonthsNoOverflow($months);

        return [
            'cutoff' => $cutoff,
            'deleted' => $this->fillLevels->pruneHistoryBefore($cutoff),
        ];
    }
}
