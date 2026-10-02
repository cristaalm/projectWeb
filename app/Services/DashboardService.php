<?php

namespace App\Services;

use App\Http\Resources\ScanResource;
use App\Repositories\DashboardRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use InvalidArgumentException;

class DashboardService
{
    public const DEFAULT_PERIOD = 'last_30_days';

    /** Periodos que ofrece el selector del panel. */
    public const PERIODS = [
        'last_7_days',
        'last_30_days',
        'this_month',
        'last_month',
        'this_year',
        'last_12_months',
    ];

    private const TOP_LIMIT = 5;

    private const RECENT_SCANS_LIMIT = 8;

    /** Días sin escaneos a partir de los cuales un contenedor activo se señala. */
    private const SILENT_CONTAINER_DAYS = 7;

    /** Un contenedor se señala por fallos solo con una muestra mínima de escaneos. */
    private const HIGH_FAILURE_MIN_SCANS = 10;

    private const HIGH_FAILURE_MIN_RATE = 0.5;

    public function __construct(
        private readonly DashboardRepository $dashboard,
    ) {}

    public function superadminOverview(string $periodKey, Request $request): array
    {
        $now = now();
        $period = self::resolvePeriod($periodKey, $now);
        ['from' => $from, 'to' => $to] = $period;

        $current = $this->dashboard->totals($from, $to);
        $previous = $this->dashboard->totals($period['previous_from'], $period['previous_to']);

        return [
            'period' => $period,
            'kpis' => [
                'valid_scans' => [
                    'value' => $current['valid_scans'],
                    'previous' => $previous['valid_scans'],
                ],
                'points_awarded' => [
                    'value' => $current['points_awarded'],
                    'previous' => $previous['points_awarded'],
                ],
                'active_users' => [
                    'value' => $current['active_users'],
                    'previous' => $previous['active_users'],
                    'total' => $this->dashboard->totalUsers(),
                ],
                'containers' => $this->dashboard->containerCounts(),
            ],
            'timeline' => $this->timeline($period),
            'materials' => $this->dashboard->materials($from, $to)->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'slug' => $row->slug,
                'valid_scans' => (int) $row->valid_scans,
                'failed_scans' => (int) $row->failed_scans,
                'points' => (int) $row->points,
            ])->all(),
            'attention' => [
                'pending_rewards' => $this->dashboard->pendingRewards(),
                'silent_container_days' => self::SILENT_CONTAINER_DAYS,
                'silent_containers' => $this->dashboard
                    ->silentContainers($now->copy()->subDays(self::SILENT_CONTAINER_DAYS))
                    ->map(fn ($row) => [
                        'id' => $row->id,
                        'name' => $row->name,
                        'serial_number' => $row->serial_number,
                        'last_scan_at' => $row->last_scan_at
                            ? CarbonImmutable::parse($row->last_scan_at)->toJSON()
                            : null,
                    ])->all(),
                'high_failure_containers' => $this->dashboard
                    ->highFailureContainers($from, $to, self::HIGH_FAILURE_MIN_SCANS, self::HIGH_FAILURE_MIN_RATE)
                    ->map(fn ($row) => [
                        'id' => $row->id,
                        'name' => $row->name,
                        'serial_number' => $row->serial_number,
                        'valid_scans' => (int) $row->valid_scans,
                        'failed_scans' => (int) $row->failed_scans,
                    ])->all(),
            ],
            'top_containers' => $this->dashboard->topContainers($from, $to, self::TOP_LIMIT)->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'serial_number' => $row->serial_number,
                'valid_scans' => (int) $row->valid_scans,
                'points' => (int) $row->points,
            ])->all(),
            'top_users' => $this->dashboard->topUsers($from, $to, self::TOP_LIMIT)->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'last_name' => $row->last_name,
                'avatar' => $row->avatar,
                'valid_scans' => (int) $row->valid_scans,
                'points' => (int) $row->points,
            ])->all(),
            'recent_scans' => ScanResource::collection(
                $this->dashboard->recentScans(self::RECENT_SCANS_LIMIT)
            )->resolve($request),
        ];
    }

    /**
     * Rango de fechas de un periodo del selector y el de comparación: la
     * ventana inmediata anterior con el mismo número de días. Todo en días
     * calendario `Y-m-d` de la zona horaria de `$now`.
     *
     * @return array{key: string, from: string, to: string, previous_from: string, previous_to: string, granularity: string}
     */
    public static function resolvePeriod(string $key, CarbonInterface $now): array
    {
        $today = CarbonImmutable::instance($now)->startOfDay();

        [$from, $to, $granularity] = match ($key) {
            'last_7_days' => [$today->subDays(6), $today, 'day'],
            'last_30_days' => [$today->subDays(29), $today, 'day'],
            'this_month' => [$today->startOfMonth(), $today, 'day'],
            'last_month' => [
                $today->subMonthNoOverflow()->startOfMonth(),
                $today->subMonthNoOverflow()->endOfMonth()->startOfDay(),
                'day',
            ],
            'this_year' => [$today->startOfYear(), $today, 'month'],
            'last_12_months' => [$today->startOfMonth()->subMonths(11), $today, 'month'],
            default => throw new InvalidArgumentException("Periodo desconocido: {$key}"),
        };

        $days = (int) $from->diffInDays($to) + 1;
        $previousTo = $from->subDay();

        return [
            'key' => $key,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'previous_from' => $previousTo->subDays($days - 1)->toDateString(),
            'previous_to' => $previousTo->toDateString(),
            'granularity' => $granularity,
        ];
    }

    /**
     * Agrega al resumen todos los días cerrados que falten, desde el último
     * agregado hasta ayer. Devuelve el rango procesado, o null si ya estaba
     * al día (o todavía no hay actividad que resumir).
     *
     * @return array{from: string, to: string}|null
     */
    public function aggregatePending(): ?array
    {
        $from = $this->nextPendingDate();
        $yesterday = now()->subDay()->toDateString();

        if ($from === null || $from > $yesterday) {
            return null;
        }

        $this->dashboard->replaceRange($from, $yesterday);

        return ['from' => $from, 'to' => $yesterday];
    }

    /**
     * Recalcula a mano un rango de días cerrados (por ejemplo, tras corregir
     * datos directo en la base). Si el rango empieza después del primer día
     * pendiente, se extiende hacia atrás hasta él: el resumen nunca debe
     * quedar con un hueco en medio.
     *
     * @return array{from: string, to: string}
     */
    public function aggregateRange(string $from, string $to): array
    {
        $yesterday = now()->subDay()->toDateString();

        if ($from > $to) {
            throw new InvalidArgumentException('La fecha inicial no puede ser posterior a la final.');
        }

        if ($to > $yesterday) {
            throw new InvalidArgumentException('Solo se pueden agregar días ya cerrados (hasta ayer).');
        }

        $pending = $this->nextPendingDate();
        if ($pending !== null && $pending < $from) {
            $from = $pending;
        }

        $this->dashboard->replaceRange($from, $to);

        return ['from' => $from, 'to' => $to];
    }

    /** Primer día que todavía no está en el resumen. */
    private function nextPendingDate(): ?string
    {
        $last = $this->dashboard->lastAggregatedDate();

        return $last !== null
            ? CarbonImmutable::parse($last)->addDay()->toDateString()
            : $this->dashboard->firstActivityDate();
    }

    /** Serie completa del periodo: un punto por día o por mes, con ceros donde no hubo actividad. */
    private function timeline(array $period): array
    {
        $rows = $this->dashboard->timeline($period['from'], $period['to'], $period['granularity']);

        $byMonth = $period['granularity'] === 'month';
        $format = $byMonth ? 'Y-m' : 'Y-m-d';
        $cursor = CarbonImmutable::parse($period['from']);
        $end = CarbonImmutable::parse($period['to']);

        if ($byMonth) {
            $cursor = $cursor->startOfMonth();
        }

        $series = [];
        while ($cursor <= $end) {
            $bucket = $cursor->format($format);
            $row = $rows->get($bucket);

            $series[] = [
                'bucket' => $bucket,
                'valid' => (int) ($row->valid ?? 0),
                'failed' => (int) ($row->failed ?? 0),
            ];

            $cursor = $byMonth ? $cursor->addMonthNoOverflow() : $cursor->addDay();
        }

        return $series;
    }
}
