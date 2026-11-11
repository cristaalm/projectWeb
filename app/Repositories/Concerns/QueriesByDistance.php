<?php

namespace App\Repositories\Concerns;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Distancia en metros por Haversine en SQL (PostgreSQL sin PostGIS), con un
 * prefiltro por rango de latitud/longitud para no calcularla sobre toda la tabla.
 * Las filas sin coordenadas quedan fuera siempre que se filtra por radio.
 */
trait QueriesByDistance
{
    private const EARTH_RADIUS_M = 6371000;

    private const METERS_PER_DEGREE = 111320;

    /**
     * Agrega la columna `distance_m` (metros, decimal) calculada contra las
     * columnas indicadas. Una fila sin coordenadas obtiene null.
     */
    protected function selectDistance(EloquentBuilder|QueryBuilder $query, string $latColumn, string $lngColumn, float $lat, float $lng): void
    {
        [$sql, $bindings] = $this->distanceSql($latColumn, $lngColumn, $lat, $lng);

        $query->selectRaw("{$sql} AS distance_m", $bindings);
    }

    /** Deja solo las filas a `$radiusM` metros o menos. */
    protected function whereWithinRadius(EloquentBuilder|QueryBuilder $query, string $latColumn, string $lngColumn, float $lat, float $lng, float $radiusM): void
    {
        $latDelta = $radiusM / self::METERS_PER_DEGREE;
        // Cerca de los polos cos() tiende a 0; el tope evita dividir entre ~0.
        $lngDelta = min(180, $radiusM / (self::METERS_PER_DEGREE * max(cos(deg2rad($lat)), 0.01)));

        [$sql, $bindings] = $this->distanceSql($latColumn, $lngColumn, $lat, $lng);

        $query->whereBetween($latColumn, [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween($lngColumn, [$lng - $lngDelta, $lng + $lngDelta])
            ->whereRaw("{$sql} <= ?", [...$bindings, $radiusM]);
    }

    /** @return array{0: string, 1: array<int, float>} */
    private function distanceSql(string $latColumn, string $lngColumn, float $lat, float $lng): array
    {
        // LEAST(1, …) evita que un redondeo deje el argumento de ASIN apenas por encima de 1.
        $sql = '('.self::EARTH_RADIUS_M." * 2 * ASIN(LEAST(1, SQRT(
            POWER(SIN(RADIANS({$latColumn} - ?) / 2), 2)
            + COS(RADIANS(?)) * COS(RADIANS({$latColumn})) * POWER(SIN(RADIANS({$lngColumn} - ?) / 2), 2)
        ))))";

        return [$sql, [$lat, $lat, $lng]];
    }
}
