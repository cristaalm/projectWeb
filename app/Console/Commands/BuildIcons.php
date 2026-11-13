<?php

namespace App\Console\Commands;

use App\Support\BadgeIcons;
use App\Support\TypeShopIcons;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Genera los SVG de los catálogos cerrados de iconos (categorías de comercio e
 * insignias) a partir de los sets de Iconify que ya trae el frontend
 * (`node_modules/@iconify-json/<prefijo>`). Los SVG se versionan en el repo y los
 * consumen la web y la app móvil vía `icon_url`, así que esto solo hace falta al
 * cambiar alguno de los catálogos.
 */
class BuildIcons extends Command
{
    protected $signature = 'icons:build';

    protected $description = 'Generar los SVG de los catálogos de iconos (categorías de comercio e insignias) desde @iconify-json';

    /** @var list<class-string> Catálogos con PUBLIC_DIR y sources(): [archivo => 'prefijo:nombre'] */
    private const CATALOGS = [TypeShopIcons::class, BadgeIcons::class];

    /** @var array<string, array> sets de Iconify ya leídos, por prefijo */
    private array $sets = [];

    public function handle(): int
    {
        $missing = [];

        foreach (self::CATALOGS as $catalog) {
            $target = public_path($catalog::PUBLIC_DIR);

            File::ensureDirectoryExists($target);
            File::cleanDirectory($target);

            foreach ($catalog::sources() as $file => $source) {
                [$prefix, $name] = explode(':', $source, 2);
                $set = $this->set($prefix);
                $icon = $set ? $this->resolve($set, $name) : null;

                if (! $icon) {
                    $missing[] = $source;

                    continue;
                }

                $width = $icon['width'] ?? $set['width'] ?? 24;
                $height = $icon['height'] ?? $set['height'] ?? 24;

                File::put(
                    "{$target}/{$file}.svg",
                    sprintf(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%2$d" viewBox="0 0 %1$d %2$d">%3$s</svg>' . "\n",
                        $width,
                        $height,
                        $icon['body'],
                    ),
                );
            }

            $this->info(sprintf('%d iconos generados en public/%s.', count($catalog::sources()), $catalog::PUBLIC_DIR));
        }

        if ($missing) {
            $this->error('Iconos que no se encontraron (¿falta `pnpm install`?): ' . implode(', ', $missing));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function set(string $prefix): ?array
    {
        if (! array_key_exists($prefix, $this->sets)) {
            $path = base_path("node_modules/@iconify-json/{$prefix}/icons.json");
            $this->sets[$prefix] = File::exists($path) ? json_decode(File::get($path), true) : null;
        }

        return $this->sets[$prefix];
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
