<?php

namespace App\Repositories;

use App\Enums\ContainerStatus;
use App\Enums\RewardStatus;
use App\Enums\ScanStatus;
use App\Models\Container;
use App\Models\DailyScanStat;
use App\Models\DailyUserStat;
use App\Models\Reward;
use App\Models\Scan;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Consultas del panel principal. Las cifras por periodo salen de las tablas
 * de resumen (`daily_scan_stats`, `daily_user_stats`) para los días ya
 * agregados, y de las tablas reales para los que todavía no lo están (el día
 * en curso y cualquier día cerrado que el job aún no haya procesado). Las
 * mismas consultas crudas llenan el resumen y calculan esa parte en vivo,
 * para que ambos caminos no puedan divergir.
 *
 * Todas las fechas son días calendario `Y-m-d` en la zona horaria de la app,
 * que es como están guardados `scanned_at` y `created_at`.
 */
class DashboardRepository
{
    /** Último día agregado, memorizado durante la petición ('' = ninguno). */
    private ?string $cutoff = null;

    // ---------------------------------------------------------------------
    // Resumen diario
    // ---------------------------------------------------------------------

    /**
     * Último día guardado en el resumen. El job siempre procesa rangos
     * contiguos hasta ayer, así que todo día anterior o igual a este ya está
     * agregado (aunque no tenga filas, si no hubo actividad).
     */
    public function lastAggregatedDate(): ?string
    {
        $dates = array_filter([
            DailyScanStat::max('date'),
            DailyUserStat::max('date'),
        ]);

        return $dates ? Carbon::parse(max($dates))->toDateString() : null;
    }

    /** Día de la actividad más antigua registrada (escaneo o insignia). */
    public function firstActivityDate(): ?string
    {
        $dates = array_filter([
            Scan::min('scanned_at'),
            DB::table('badge_earnings')->min('created_at'),
        ]);

        return $dates ? Carbon::parse(min($dates))->toDateString() : null;
    }

    /**
     * Recalcula el resumen de un rango de días cerrados: borra lo que hubiera
     * y lo vuelve a generar desde las tablas reales, en una sola transacción.
     */
    public function replaceRange(string $from, string $to): void
    {
        DB::transaction(function () use ($from, $to) {
            DailyScanStat::whereBetween('date', [$from, $to])->delete();
            DailyUserStat::whereBetween('date', [$from, $to])->delete();

            DB::table('daily_scan_stats')->insertUsing(
                ['date', 'container_id', 'material_type_id', 'valid_scans', 'failed_scans', 'points_awarded'],
                $this->rawScanStats($from, $to),
            );

            DB::table('daily_user_stats')->insertUsing(
                ['date', 'user_id', 'valid_scans', 'scan_points', 'badge_points'],
                $this->rawUserStats($from, $to),
            );
        });

        $this->cutoff = null;
    }

    // ---------------------------------------------------------------------
    // Cifras por periodo
    // ---------------------------------------------------------------------

    /** @return array{valid_scans: int, points_awarded: int, active_users: int} */
    public function totals(string $from, string $to): array
    {
        $validScans = DB::query()->fromSub($this->scanStats($from, $to), 's')->sum('valid_scans');

        $users = DB::query()->fromSub($this->userStats($from, $to), 'u')
            ->selectRaw('coalesce(sum(scan_points + badge_points), 0) as points')
            ->selectRaw('count(distinct user_id) filter (where valid_scans > 0) as active_users')
            ->first();

        return [
            'valid_scans' => (int) $validScans,
            'points_awarded' => (int) $users->points,
            'active_users' => (int) $users->active_users,
        ];
    }

    /**
     * Escaneos válidos y fallidos por día o por mes. Solo trae los periodos
     * con actividad; los huecos los rellena DashboardService.
     *
     * @return Collection<string, object{bucket: string, valid: int, failed: int}>
     */
    public function timeline(string $from, string $to, string $granularity): Collection
    {
        $format = $granularity === 'month' ? 'YYYY-MM' : 'YYYY-MM-DD';

        return DB::query()->fromSub($this->scanStats($from, $to), 's')
            ->selectRaw("to_char(date, '{$format}') as bucket")
            ->selectRaw('sum(valid_scans) as valid, sum(failed_scans) as failed')
            ->groupBy('bucket')
            ->get()
            ->keyBy('bucket');
    }

    /** Todos los materiales del catálogo, con su actividad en el periodo. */
    public function materials(string $from, string $to): Collection
    {
        return DB::table('material_types as mt')
            ->leftJoinSub($this->scanStats($from, $to), 's', 's.material_type_id', '=', 'mt.id')
            ->groupBy('mt.id', 'mt.name', 'mt.slug')
            ->orderBy('mt.name')
            ->get([
                'mt.id',
                'mt.name',
                'mt.slug',
                DB::raw('coalesce(sum(s.valid_scans), 0) as valid_scans'),
                DB::raw('coalesce(sum(s.failed_scans), 0) as failed_scans'),
                DB::raw('coalesce(sum(s.points_awarded), 0) as points'),
            ]);
    }

    public function topContainers(string $from, string $to, int $limit): Collection
    {
        return $this->containerActivity($from, $to)
            ->havingRaw('sum(s.valid_scans) > 0')
            ->orderByDesc('valid_scans')
            ->orderBy('c.id')
            ->limit($limit)
            ->get();
    }

    /**
     * Contenedores con al menos `$minScans` escaneos en el periodo y una
     * proporción de fallidos igual o mayor a `$minFailureRate` (0 a 1).
     */
    public function highFailureContainers(string $from, string $to, int $minScans, float $minFailureRate): Collection
    {
        return $this->containerActivity($from, $to)
            ->havingRaw('sum(s.valid_scans + s.failed_scans) >= ?', [$minScans])
            ->havingRaw('sum(s.failed_scans) >= ?::numeric * sum(s.valid_scans + s.failed_scans)', [$minFailureRate])
            ->orderByDesc('failed_scans')
            ->orderBy('c.id')
            ->get();
    }

    /** Usuarios que más puntos ganaron en el periodo (reciclaje + insignias). */
    public function topUsers(string $from, string $to, int $limit): Collection
    {
        // Join directo a la tabla (sin el scope de soft-delete): un usuario
        // dado de baja después sigue contando en el ranking de ese periodo.
        return DB::query()->fromSub($this->userStats($from, $to), 'u')
            ->join('users', 'users.id', '=', 'u.user_id')
            ->groupBy('users.id', 'users.name', 'users.last_name', 'users.avatar')
            ->havingRaw('sum(u.scan_points + u.badge_points) > 0')
            ->orderByDesc('points')
            ->orderBy('users.id')
            ->limit($limit)
            ->get([
                'users.id',
                'users.name',
                'users.last_name',
                'users.avatar',
                DB::raw('sum(u.valid_scans) as valid_scans'),
                DB::raw('sum(u.scan_points + u.badge_points) as points'),
            ]);
    }

    // ---------------------------------------------------------------------
    // Estado actual (no depende del periodo)
    // ---------------------------------------------------------------------

    public function totalUsers(): int
    {
        return User::count();
    }

    /** @return array{active: int, total: int} */
    public function containerCounts(): array
    {
        return [
            'active' => Container::where('status', ContainerStatus::ACTIVE)->count(),
            'total' => Container::count(),
        ];
    }

    public function pendingRewards(): int
    {
        return Reward::where('status', RewardStatus::PENDING)->count();
    }

    /**
     * Contenedores activos sin actividad desde `$since`: su último escaneo
     * (o su alta, si nunca han registrado uno) es anterior a esa fecha.
     */
    public function silentContainers(CarbonInterface $since): Collection
    {
        return DB::table('containers as c')
            ->leftJoin('scans as s', 's.container_id', '=', 'c.id')
            ->where('c.status', ContainerStatus::ACTIVE->value)
            ->groupBy('c.id', 'c.name', 'c.serial_number', 'c.created_at')
            ->havingRaw('coalesce(max(s.scanned_at), c.created_at) < ?', [$since])
            ->orderByRaw('max(s.scanned_at) asc nulls first')
            ->orderBy('c.id')
            ->get([
                'c.id',
                'c.name',
                'c.serial_number',
                DB::raw('max(s.scanned_at) as last_scan_at'),
            ]);
    }

    public function recentScans(int $limit): EloquentCollection
    {
        // withTrashed: igual que el listado de escaneos, el escaneo sigue
        // mostrando a su usuario aunque se haya dado de baja después.
        return Scan::query()
            ->with([
                'user' => fn ($q) => $q->withTrashed(),
                'container',
                'materialType',
            ])
            ->orderByDesc('scanned_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    // ---------------------------------------------------------------------
    // Fuentes: resumen para lo ya agregado + tablas reales para el resto
    // ---------------------------------------------------------------------

    private function containerActivity(string $from, string $to): Builder
    {
        return DB::query()->fromSub($this->scanStats($from, $to), 's')
            ->join('containers as c', 'c.id', '=', 's.container_id')
            ->groupBy('c.id', 'c.name', 'c.serial_number')
            ->select([
                'c.id',
                'c.name',
                'c.serial_number',
                DB::raw('sum(s.valid_scans) as valid_scans'),
                DB::raw('sum(s.failed_scans) as failed_scans'),
                DB::raw('sum(s.points_awarded) as points'),
            ]);
    }

    /** Filas (date, container_id, material_type_id, valid_scans, failed_scans, points_awarded) del rango. */
    private function scanStats(string $from, string $to): Builder
    {
        $summary = DB::table('daily_scan_stats')
            ->whereBetween('date', [$from, $to])
            ->select(['date', 'container_id', 'material_type_id', 'valid_scans', 'failed_scans', 'points_awarded']);

        $liveFrom = $this->liveFrom($from);

        return $liveFrom > $to ? $summary : $summary->unionAll($this->rawScanStats($liveFrom, $to));
    }

    /** Filas (date, user_id, valid_scans, scan_points, badge_points) del rango. */
    private function userStats(string $from, string $to): Builder
    {
        $summary = DB::table('daily_user_stats')
            ->whereBetween('date', [$from, $to])
            ->select(['date', 'user_id', 'valid_scans', 'scan_points', 'badge_points']);

        $liveFrom = $this->liveFrom($from);

        return $liveFrom > $to ? $summary : $summary->unionAll($this->rawUserStats($liveFrom, $to));
    }

    /**
     * Primer día del rango que todavía no está en el resumen y por tanto hay
     * que leer de las tablas reales.
     */
    private function liveFrom(string $from): string
    {
        $this->cutoff ??= $this->lastAggregatedDate() ?? '';

        if ($this->cutoff === '' || $this->cutoff < $from) {
            return $from;
        }

        return Carbon::parse($this->cutoff)->addDay()->toDateString();
    }

    private function rawScanStats(string $from, string $to): Builder
    {
        $success = ScanStatus::SUCCESS->value;
        $failed = ScanStatus::FAILED->value;

        return DB::table('scans')
            ->where('scanned_at', '>=', $from.' 00:00:00')
            ->where('scanned_at', '<', $this->dayAfter($to).' 00:00:00')
            ->groupByRaw('scanned_at::date, container_id, material_type_id')
            ->selectRaw('scanned_at::date as date, container_id, material_type_id')
            ->selectRaw("count(*) filter (where scan_status = {$success}) as valid_scans")
            ->selectRaw("count(*) filter (where scan_status = {$failed}) as failed_scans")
            ->selectRaw("coalesce(sum(points_awarded) filter (where scan_status = {$success}), 0) as points_awarded");
    }

    private function rawUserStats(string $from, string $to): Builder
    {
        $start = $from.' 00:00:00';
        $end = $this->dayAfter($to).' 00:00:00';

        $scans = DB::table('scans')
            ->where('scan_status', ScanStatus::SUCCESS->value)
            ->where('scanned_at', '>=', $start)
            ->where('scanned_at', '<', $end)
            ->groupByRaw('scanned_at::date, user_id')
            ->selectRaw('scanned_at::date as date, user_id, count(*) as valid_scans, coalesce(sum(points_awarded), 0) as scan_points, 0 as badge_points');

        $badges = DB::table('badge_earnings')
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $end)
            ->groupByRaw('created_at::date, user_id')
            ->selectRaw('created_at::date as date, user_id, 0 as valid_scans, 0 as scan_points, coalesce(sum(points), 0) as badge_points');

        // Un usuario puede aparecer en ambas fuentes el mismo día: se funden
        // en una sola fila, que es la forma de daily_user_stats.
        return DB::query()->fromSub($scans->unionAll($badges), 't')
            ->groupBy('date', 'user_id')
            ->selectRaw('date, user_id, sum(valid_scans) as valid_scans, sum(scan_points) as scan_points, sum(badge_points) as badge_points');
    }

    private function dayAfter(string $date): string
    {
        return Carbon::parse($date)->addDay()->toDateString();
    }
}
