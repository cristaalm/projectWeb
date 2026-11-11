<?php

return [
    /*
     * Meses que se conserva el historial de lecturas de llenado
     * (container_fill_readings) antes de que lo purgue
     * `containers:prune-fill-readings`. Un valor vacío o inválido en el
     * entorno cae al default: nunca debe quedar en 0, porque borraría todo.
     */
    'fill_readings_retention_months' => max(1, (int) (env('CONTAINER_FILL_READINGS_RETENTION_MONTHS') ?: 12)),
];
