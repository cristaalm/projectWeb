<?php

namespace App\Console\Commands;

use App\Support\TypeShopIcons;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Genera los SVG de `public/icons/type-shop/` a partir del set de Material Design
 * Icons que ya trae el frontend (`@iconify-json/mdi`, en node_modules). Los SVG se
 * versionan en el repo, así que esto solo hace falta al cambiar App\Support\TypeShopIcons.
 */
class BuildTypeShopIcons extends Command
{
    protected $signature = 'type-shop:build-icons';

    protected $description = 'Generar los SVG del catálogo de iconos de categorías de comercio (desde @iconify-json/mdi)';

    public function handle(): int
    {
        $source = base_path('node_modules/@iconify-json/mdi/icons.json');

        if (! File::exists($source)) {
            $this->error("No se encontró {$source}. Corre `pnpm install` primero.");

            return self::FAILURE;
        }

        $set = json_decode(File::get($source), true);
        $target = public_path(TypeShopIcons::PUBLIC_DIR);

        File::ensureDirectoryExists($target);
        File::cleanDirectory($target);

        $missing = [];

        foreach (TypeShopIcons::names() as $name) {
            $icon = $this->resolve($set, $name);

            if (! $icon) {
                $missing[] = $name;

                continue;
            }

            $width = $icon['width'] ?? $set['width'] ?? 24;
            $height = $icon['height'] ?? $set['height'] ?? 24;

            File::put(
                "{$target}/{$name}.svg",
                sprintf(
                    '<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%2$d" viewBox="0 0 %1$d %2$d">%3$s</svg>' . "\n",
                    $width,
                    $height,
                    $icon['body'],
                ),
            );
        }

        if ($missing) {
            $this->error('Iconos que no existen en MDI: ' . implode(', ', $missing));

            return self::FAILURE;
        }

        $this->info(sprintf('%d iconos generados en public/%s.', count(TypeShopIcons::names()), TypeShopIcons::PUBLIC_DIR));

        return self::SUCCESS;
    }

    /** Resuelve un icono directo o un alias (los alias de Iconify apuntan a un `parent`). */
    private function resolve(array $set, string $name): ?array
    {
        if (isset($set['icons'][$name])) {
            return $set['icons'][$name];
        }

        $parent = $set['aliases'][$name]['parent'] ?? null;

        return $parent ? $this->resolve($set, $parent) : null;
    }
}
