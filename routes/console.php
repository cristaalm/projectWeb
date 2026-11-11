<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Resumen diario del panel. Corre cada hora y no solo a medianoche: procesa
// todos los días cerrados que falten, así que si el servidor estaba dormido
// o caído al cambiar el día se pone al corriente en la siguiente corrida.
Schedule::command('dashboard:aggregate-daily')->hourly()->withoutOverlapping();

// Purga del historial de llenado de contenedores según el plazo de retención
// (config/containers.php). Una vez al día basta: el plazo se mide en meses.
Schedule::command('containers:prune-fill-readings')->dailyAt('03:30')->withoutOverlapping();
