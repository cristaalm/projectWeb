<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Cálculo puro de los rangos de fechas del panel (sin base de datos): el
 * periodo elegido y la ventana anterior contra la que se compara.
 */
class DashboardPeriodTest extends TestCase
{
    private function resolve(string $key, string $now = '2026-10-02 15:30'): array
    {
        return DashboardService::resolvePeriod($key, CarbonImmutable::parse($now, 'America/Mexico_City'));
    }

    public function test_last_7_days_includes_today_and_compares_with_the_previous_week(): void
    {
        $this->assertSame([
            'key' => 'last_7_days',
            'from' => '2026-09-26',
            'to' => '2026-10-02',
            'previous_from' => '2026-09-19',
            'previous_to' => '2026-09-25',
            'granularity' => 'day',
        ], $this->resolve('last_7_days'));
    }

    public function test_last_30_days_includes_today(): void
    {
        $period = $this->resolve('last_30_days');

        $this->assertSame(['2026-09-03', '2026-10-02'], [$period['from'], $period['to']]);
        $this->assertSame(['2026-08-04', '2026-09-02'], [$period['previous_from'], $period['previous_to']]);
        $this->assertSame('day', $period['granularity']);
    }

    public function test_this_month_runs_from_the_first_to_today(): void
    {
        $period = $this->resolve('this_month');

        $this->assertSame(['2026-10-01', '2026-10-02'], [$period['from'], $period['to']]);
        $this->assertSame(['2026-09-29', '2026-09-30'], [$period['previous_from'], $period['previous_to']]);
    }

    public function test_last_month_is_the_whole_previous_calendar_month(): void
    {
        $period = $this->resolve('last_month');

        $this->assertSame(['2026-09-01', '2026-09-30'], [$period['from'], $period['to']]);
        $this->assertSame(['2026-08-02', '2026-08-31'], [$period['previous_from'], $period['previous_to']]);
        $this->assertSame('day', $period['granularity']);
    }

    public function test_last_month_does_not_overflow_on_the_31st(): void
    {
        $period = $this->resolve('last_month', '2026-03-31 09:00');

        $this->assertSame(['2026-02-01', '2026-02-28'], [$period['from'], $period['to']]);
    }

    public function test_this_year_is_grouped_by_month(): void
    {
        $period = $this->resolve('this_year');

        $this->assertSame(['2026-01-01', '2026-10-02'], [$period['from'], $period['to']]);
        $this->assertSame(['2025-04-01', '2025-12-31'], [$period['previous_from'], $period['previous_to']]);
        $this->assertSame('month', $period['granularity']);
    }

    public function test_last_12_months_starts_on_the_first_day_of_the_month_eleven_months_ago(): void
    {
        $period = $this->resolve('last_12_months');

        $this->assertSame(['2025-11-01', '2026-10-02'], [$period['from'], $period['to']]);
        $this->assertSame('month', $period['granularity']);
    }

    public function test_unknown_period_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->resolve('forever');
    }
}
