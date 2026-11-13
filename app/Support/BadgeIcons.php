<?php

namespace App\Support;

/**
 * Catálogo cerrado de iconos para las insignias (`badge.icon`) y fuente única de
 * la lista: la validación, el selector del admin (GET /badges/icons) y los SVG
 * salen de aquí.
 *
 * Las claves son los nombres que ya guarda la columna (formato Iconify de la
 * plantilla, `bx-*`); `source` es el icono real de Boxicons (`prefijo:nombre`)
 * del que se genera el SVG. Ojo con `bx-bxs-tree`: es la versión sólida (`bxs`).
 *
 * Los SVG viven en `public/icons/badges/<clave>.svg` y se generan con
 * `php artisan icons:build` — volver a correrlo después de tocar esta lista.
 */
final class BadgeIcons
{
    /** Icono que se usa cuando una insignia no tiene uno asignado (`icon` = null). */
    public const DEFAULT = 'bx-medal';

    public const PUBLIC_DIR = 'icons/badges';

    /** @var array<string, array{label: string, source: string}> */
    public const ICONS = [
        'bx-recycle' => ['label' => 'Reciclaje', 'source' => 'bx:recycle'],
        'bx-leaf' => ['label' => 'Hoja', 'source' => 'bx:leaf'],
        'bx-bxs-tree' => ['label' => 'Árbol', 'source' => 'bxs:tree'],
        'bx-water' => ['label' => 'Agua', 'source' => 'bx:water'],
        'bx-world' => ['label' => 'Mundo', 'source' => 'bx:world'],
        'bx-medal' => ['label' => 'Medalla', 'source' => 'bx:medal'],
        'bx-trophy' => ['label' => 'Trofeo', 'source' => 'bx:trophy'],
        'bx-award' => ['label' => 'Premio', 'source' => 'bx:award'],
        'bx-badge-check' => ['label' => 'Insignia verificada', 'source' => 'bx:badge-check'],
        'bx-sun' => ['label' => 'Sol', 'source' => 'bx:sun'],
    ];

    /** @return list<string> */
    public static function names(): array
    {
        return array_keys(self::ICONS);
    }

    /** @return array<string, string> nombre de archivo => icono Iconify (`prefijo:nombre`) */
    public static function sources(): array
    {
        return array_map(fn (array $icon) => $icon['source'], self::ICONS);
    }

    /** URL del SVG; si la insignia no tiene icono (o es uno desconocido) usa el predeterminado. */
    public static function url(?string $icon): string
    {
        $name = $icon && array_key_exists($icon, self::ICONS) ? $icon : self::DEFAULT;

        return asset(self::PUBLIC_DIR . '/' . $name . '.svg');
    }

    /** @return list<array{name: string, label: string, url: string}> */
    public static function catalog(): array
    {
        return array_map(fn (string $name) => [
            'name' => $name,
            'label' => self::ICONS[$name]['label'],
            'url' => self::url($name),
        ], self::names());
    }
}
