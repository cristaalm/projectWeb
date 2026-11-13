<?php

namespace App\Support;

/**
 * Nivel visual ("tier") de una insignia según qué tan exigente es su meta
 * (`recycles_required`). Es solo presentación — no afecta progreso ni puntos —,
 * pero vive en el backend para que la web y la app móvil pinten el emblema con
 * exactamente los mismos colores: fondo `background` e icono `color`.
 */
final class BadgeTiers
{
    /**
     * Ordenados de menor a mayor; aplica el último cuyo `min_recycles` se alcanza.
     *
     * @var list<array{key: string, label: string, min_recycles: int, background: string, color: string}>
     */
    public const TIERS = [
        ['key' => 'brote', 'label' => 'Brote', 'min_recycles' => 0, 'background' => '#7CB88F', 'color' => '#1B3A2B'],
        ['key' => 'musgo', 'label' => 'Musgo', 'min_recycles' => 10, 'background' => '#4C8C5A', 'color' => '#1B3A2B'],
        ['key' => 'bosque', 'label' => 'Bosque', 'min_recycles' => 25, 'background' => '#2F5F3F', 'color' => '#FFFFFF'],
        ['key' => 'follaje', 'label' => 'Follaje', 'min_recycles' => 50, 'background' => '#1B3A2B', 'color' => '#FFFFFF'],
    ];

    /** @return array{key: string, label: string, min_recycles: int, background: string, color: string} */
    public static function forRecycles(int $recyclesRequired): array
    {
        $tier = self::TIERS[0];

        foreach (self::TIERS as $candidate) {
            if ($recyclesRequired >= $candidate['min_recycles']) {
                $tier = $candidate;
            }
        }

        return $tier;
    }
}
