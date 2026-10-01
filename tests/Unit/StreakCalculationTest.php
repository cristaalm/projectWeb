<?php

namespace Tests\Unit;

use App\Services\StreakService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Cálculo puro de la racha (sin base de datos): solo las reglas de días
 * consecutivos de StreakService.
 */
class StreakCalculationTest extends TestCase
{
    private function at(string $datetime): CarbonImmutable
    {
        return CarbonImmutable::parse($datetime, 'America/Mexico_City');
    }

    public function test_first_recycle_starts_the_streak_at_one(): void
    {
        $this->assertSame(1, StreakService::nextStreak(0, null, $this->at('2026-10-01 10:00')));
    }

    public function test_recycling_again_the_same_day_does_not_change_the_streak(): void
    {
        $this->assertSame(4, StreakService::nextStreak(4, $this->at('2026-10-01 08:00'), $this->at('2026-10-01 23:59')));
    }

    public function test_recycling_the_next_day_extends_the_streak(): void
    {
        $this->assertSame(5, StreakService::nextStreak(4, $this->at('2026-09-30 23:59'), $this->at('2026-10-01 00:01')));
    }

    public function test_skipping_a_day_restarts_the_streak(): void
    {
        $this->assertSame(1, StreakService::nextStreak(4, $this->at('2026-09-29 12:00'), $this->at('2026-10-01 12:00')));
    }

    public function test_streak_is_alive_today_and_yesterday_only(): void
    {
        $now = $this->at('2026-10-01 12:00');

        $this->assertTrue(StreakService::isAlive($this->at('2026-10-01 00:00'), $now));
        $this->assertTrue(StreakService::isAlive($this->at('2026-09-30 00:00'), $now));
        $this->assertFalse(StreakService::isAlive($this->at('2026-09-29 23:59'), $now));
        $this->assertFalse(StreakService::isAlive(null, $now));
    }
}
