<?php

namespace App\Console\Commands;

use App\Services\ContainerFillLevelService;
use Illuminate\Console\Command;

class PruneContainerFillReadings extends Command
{
    protected $signature = 'containers:prune-fill-readings';

    protected $description = 'Borrar del historial de llenado las lecturas más viejas que el plazo de retención (CONTAINER_FILL_READINGS_RETENTION_MONTHS)';

    public function handle(ContainerFillLevelService $fillLevels): int
    {
        $result = $fillLevels->pruneHistory();

        $this->info(sprintf(
            'Historial de llenado: %d lecturas recibidas antes del %s borradas.',
            $result['deleted'],
            $result['cutoff']->format('Y-m-d H:i'),
        ));

        return self::SUCCESS;
    }
}
