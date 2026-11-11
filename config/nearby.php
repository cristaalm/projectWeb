<?php

/*
 * Parámetros de los endpoints "cerca de ti" de la app móvil (/api/nearby/*).
 * Decisiones tomadas con el usuario el 2026-11-11 (docs/context/ENDPOINTS_CERCANOS.md,
 * sección 5). Valores fijos a propósito: no son ajustes de entorno.
 */
return [
    // Distancia máxima, en metros, para que un comercio o contenedor cuente como cercano.
    'radius_m' => 5000,

    // Resultados por defecto y tope del atributo opcional `limit` (uno mayor se topa, no es error).
    'default_limit' => 5,
    'max_limit' => 50,

    // Una ubicación con peor precisión que esta (en metros) se considera inválida.
    'max_accuracy_m' => 500,

    // Antigüedad máxima de `captured_at` y margen para relojes de teléfono adelantados.
    'max_location_age_minutes' => 10,
    'future_tolerance_minutes' => 5,

    // Endpoint de recompensas: cuántas pueden salir del mismo comercio antes de
    // completar la lista con más de ese mismo comercio.
    'rewards_per_shop' => 2,

    // Endpoint de contenedores: porcentaje desde el que un compartimento cuenta
    // como lleno, y horas sin reporte tras las que su dato se marca desactualizado.
    'container_full_level' => 90,
    'container_stale_after_hours' => 6,
];
