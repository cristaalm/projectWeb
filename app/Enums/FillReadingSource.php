<?php

namespace App\Enums;

/** Por dónde llegó una lectura de llenado de un contenedor. */
enum FillReadingSource: int
{
    case SCAN = 1;
    case REPORT = 2;

    public function label(): string
    {
        return match ($this) {
            self::SCAN => 'Escaneo',
            self::REPORT => 'Reporte periódico',
        };
    }
}
