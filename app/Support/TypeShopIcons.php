<?php

namespace App\Support;

/**
 * Catálogo cerrado de iconos para las categorías de comercio (`type_shop.icon`).
 *
 * Las claves son nombres de Material Design Icons (https://pictogrammers.com/library/mdi/)
 * sin prefijo — la misma librería existe para web, Flutter (`material_design_icons_flutter`),
 * React Native (`MaterialCommunityIcons`) y nativo, así que la app móvil puede pintar el
 * icono con su librería a partir del nombre, o simplemente cargar el SVG de `url()`.
 *
 * Los SVG viven en `public/icons/type-shop/<nombre>.svg` y se generan con
 * `php artisan icons:build` — volver a correrlo después de tocar esta lista.
 */
final class TypeShopIcons
{
    public const DEFAULT = 'store';

    /** Carpeta pública (relativa a `public/`) donde viven los SVG. */
    public const PUBLIC_DIR = 'icons/type-shop';

    /** @var array<string, string> nombre MDI => etiqueta en español */
    public const ICONS = [
        'store' => 'Tienda',
        'cart' => 'Supermercado',
        'shopping' => 'Compras',
        'pill' => 'Farmacia',
        'hospital-box' => 'Salud',
        'tooth' => 'Dental',
        'glasses' => 'Óptica',
        'silverware-fork-knife' => 'Restaurante',
        'coffee' => 'Cafetería',
        'bread-slice' => 'Panadería',
        'cupcake' => 'Repostería',
        'ice-cream' => 'Helados',
        'pizza' => 'Pizzería',
        'hamburger' => 'Comida rápida',
        'food-apple' => 'Frutas y verduras',
        'beer' => 'Bar',
        'glass-cocktail' => 'Coctelería',
        'gas-station' => 'Gasolinera',
        'car' => 'Automotriz',
        'bicycle' => 'Bicicletas',
        'warehouse' => 'Bodega',
        'hammer-wrench' => 'Ferretería',
        'cellphone' => 'Celulares',
        'laptop' => 'Electrónica',
        'printer' => 'Imprenta',
        'gamepad-variant' => 'Videojuegos',
        'book-open-page-variant' => 'Librería',
        'pencil' => 'Papelería',
        'school' => 'Educación',
        'teddy-bear' => 'Juguetes',
        'tshirt-crew' => 'Ropa',
        'shoe-sneaker' => 'Calzado',
        'diamond-stone' => 'Joyería',
        'lipstick' => 'Belleza',
        'content-cut' => 'Estética',
        'dumbbell' => 'Gimnasio',
        'paw' => 'Mascotas',
        'flower' => 'Florería',
        'gift' => 'Regalos',
        'sofa' => 'Muebles',
        'home' => 'Hogar',
        'washing-machine' => 'Lavandería',
        'bed' => 'Hotel',
        'airplane' => 'Viajes',
        'movie-open' => 'Entretenimiento',
        'music' => 'Música',
        'cash' => 'Servicios financieros',
        'recycle' => 'Reciclaje',
        'leaf' => 'Ecológico',
    ];

    /** @return list<string> */
    public static function names(): array
    {
        return array_keys(self::ICONS);
    }

    /** @return array<string, string> nombre de archivo => icono Iconify (`prefijo:nombre`) */
    public static function sources(): array
    {
        return array_combine(self::names(), array_map(fn (string $name) => "mdi:{$name}", self::names()));
    }

    public static function url(?string $icon): ?string
    {
        if (! $icon || ! array_key_exists($icon, self::ICONS)) {
            return null;
        }

        return asset(self::PUBLIC_DIR . '/' . $icon . '.svg');
    }

    /** @return list<array{name: string, label: string, url: string}> */
    public static function catalog(): array
    {
        return array_map(fn (string $name) => [
            'name' => $name,
            'label' => self::ICONS[$name],
            'url' => self::url($name),
        ], self::names());
    }
}
