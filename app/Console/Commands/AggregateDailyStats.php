<?php

namespace App\Console\Commands;

use App\Services\DashboardService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class AggregateDailyStats extends Command
{
    protected $signature = 'dashboard:aggregate-daily
                            {--from= : Primer día a recalcular (Y-m-d)}
                            {--to= : Último día a recalcular (Y-m-d); por defecto, ayer}';

    protected $description = 'Llenar el resumen diario del panel con los días cerrados que falten (o recalcular un rango)';

    public function handle(DashboardService $dashboard): int
    {
        try {
            $range = $this->option('from') !== null || $this->option('to') !== null
                ? $this->aggregateRange($dashboard)
                : $dashboard->aggregatePending();
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($range === null) {
            $this->info('El resumen diario ya está al día.');

            return self::SUCCESS;
        }

        $this->info("Resumen diario actualizado del {$range['from']} al {$range['to']}.");

        return self::SUCCESS;
    }

    private function aggregateRange(DashboardService $dashboard): array
    {
        if ($this->option('from') === null) {
            throw new InvalidArgumentException('Indica --from para recalcular un rango.');
        }

        return $dashboard->aggregateRange(
            $this->parseDate($this->option('from')),
            $this->parseDate($this->option('to') ?? now()->subDay()->toDateString()),
        );
    }

    private function parseDate(string $value): string
    {
        $date = Carbon::createFromFormat('!Y-m-d', $value);

        if ($date === false || $date === null || $date->toDateString() !== $value) {
            throw new InvalidArgumentException("Fecha inválida: {$value} (formato esperado Y-m-d).");
        }

        return $value;
    }
}
