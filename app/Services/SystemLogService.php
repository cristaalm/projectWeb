<?php

namespace App\Services;

use App\Exceptions\SystemLogException;
use Illuminate\Support\Carbon;

/**
 * Consulta de los logs del servidor para diagnosticar producción sin acceso
 * a la consola del hosting. Solo se pueden leer los archivos de la lista
 * fija de `sources()`: el endpoint nunca recibe una ruta, solo una clave.
 */
class SystemLogService
{
    public const DEFAULT_LINES = 200;

    public const MAX_LINES = 1000;

    /**
     * Tope de bytes que se recorren desde el final del archivo. Acota el
     * costo de una búsqueda sin coincidencias en un log de cientos de MB y
     * el tamaño de la respuesta.
     */
    public const MAX_SCAN_BYTES = 2 * 1024 * 1024;

    /** Donde supervisor escribe la salida de sus programas (ver supervisord.conf). */
    private const SUPERVISOR_DIR = '/var/log/supervisor';

    private const CHUNK_BYTES = 8192;

    /**
     * Logs consultables. Los de supervisor solo existen en la imagen de
     * producción; en el entorno local de Sail esos servicios escriben a la
     * salida de Docker (`docker compose logs <servicio>`).
     *
     * @return array<string, array{label: string, path: string}>
     */
    public static function sources(): array
    {
        return [
            'laravel' => [
                'label' => 'Aplicación (Laravel)',
                'path' => storage_path('logs/laravel.log'),
            ],
            'supervisor' => [
                'label' => 'Supervisor (arranque y caídas de los procesos)',
                'path' => self::SUPERVISOR_DIR.'/supervisord.log',
            ],
            'scheduler' => [
                'label' => 'Tareas programadas',
                'path' => self::SUPERVISOR_DIR.'/scheduler.out.log',
            ],
            'scheduler-error' => [
                'label' => 'Tareas programadas (errores)',
                'path' => self::SUPERVISOR_DIR.'/scheduler.err.log',
            ],
            'queue' => [
                'label' => 'Worker de la cola',
                'path' => self::SUPERVISOR_DIR.'/queue.out.log',
            ],
            'queue-error' => [
                'label' => 'Worker de la cola (errores)',
                'path' => self::SUPERVISOR_DIR.'/queue.err.log',
            ],
            'apache' => [
                'label' => 'Apache (accesos)',
                'path' => self::SUPERVISOR_DIR.'/apache.out.log',
            ],
            'apache-error' => [
                'label' => 'Apache (errores)',
                'path' => self::SUPERVISOR_DIR.'/apache.err.log',
            ],
        ];
    }

    /** Lista de logs con su estado en este servidor. */
    public function list(): array
    {
        $list = [];

        foreach (self::sources() as $key => $source) {
            $list[] = ['source' => $key, 'label' => $source['label']] + $this->fileStatus($source['path']);
        }

        return $list;
    }

    /**
     * Últimas líneas de un log, de la más antigua a la más reciente. Con
     * `$search` solo se devuelven las líneas que contienen ese texto.
     */
    public function read(string $key, int $lines = self::DEFAULT_LINES, ?string $search = null): array
    {
        $source = self::sources()[$key] ?? null;

        if ($source === null) {
            throw new SystemLogException('El log solicitado no existe.', 404);
        }

        $status = $this->fileStatus($source['path']);

        if (! $status['available']) {
            throw new SystemLogException(
                'Ese log todavía no existe en este servidor o no se puede leer.',
                404,
            );
        }

        $tail = self::tail($source['path'], $lines, $search);

        return [
            'source' => $key,
            'label' => $source['label'],
            'size_bytes' => $status['size_bytes'],
            'modified_at' => $status['modified_at'],
            'search' => $search,
            'reached_start' => $tail['reached_start'],
            'scanned_bytes' => $tail['scanned_bytes'],
            'count' => count($tail['lines']),
            'lines' => $tail['lines'],
        ];
    }

    /**
     * Lee un archivo de atrás hacia adelante, por bloques, hasta juntar
     * `$limit` líneas o agotar `$maxBytes`: nunca carga el archivo completo,
     * que en producción puede pesar cientos de MB.
     *
     * @return array{lines: list<string>, reached_start: bool, scanned_bytes: int}
     */
    public static function tail(string $path, int $limit, ?string $search = null, int $maxBytes = self::MAX_SCAN_BYTES): array
    {
        $handle = is_file($path) && is_readable($path) ? @fopen($path, 'rb') : false;

        if ($handle === false) {
            throw new SystemLogException('No se pudo abrir el log.', 500);
        }

        try {
            // El tamaño se toma una sola vez: si el log crece mientras se
            // lee, lo nuevo queda para la siguiente consulta.
            $position = fstat($handle)['size'];
            $scanned = 0;
            $pending = '';
            $lines = [];
            $search = $search === null || $search === '' ? null : $search;

            while ($position > 0 && count($lines) < $limit && $scanned < $maxBytes) {
                $length = min(self::CHUNK_BYTES, $position, $maxBytes - $scanned);
                $position -= $length;
                $scanned += $length;

                fseek($handle, $position);
                $pieces = explode("\n", fread($handle, $length).$pending);

                // El primer fragmento puede ser una línea partida por el
                // corte del bloque: se completa con el bloque anterior.
                $pending = array_shift($pieces);

                for ($i = count($pieces) - 1; $i >= 0 && count($lines) < $limit; $i--) {
                    self::collect($lines, $pieces[$i], $search);
                }
            }

            $reachedStart = $position === 0;

            // Lo que queda en $pending solo es una línea completa si se
            // llegó al inicio del archivo.
            if ($reachedStart && count($lines) < $limit) {
                self::collect($lines, $pending, $search);
            }

            return [
                'lines' => array_reverse($lines),
                'reached_start' => $reachedStart && count($lines) < $limit,
                'scanned_bytes' => $scanned,
            ];
        } finally {
            fclose($handle);
        }
    }

    private static function collect(array &$lines, string $line, ?string $search): void
    {
        $line = rtrim($line, "\r");

        if ($line === '') {
            return;
        }

        if ($search !== null && mb_stripos($line, $search) === false) {
            return;
        }

        // Un log puede traer bytes que no son UTF-8 válido (binarios en un
        // stack trace, por ejemplo) y eso rompería el JSON de la respuesta.
        $lines[] = mb_scrub($line, 'UTF-8');
    }

    /** @return array{available: bool, size_bytes: int|null, modified_at: string|null} */
    private function fileStatus(string $path): array
    {
        clearstatcache(true, $path);

        if (! is_file($path) || ! is_readable($path)) {
            return ['available' => false, 'size_bytes' => null, 'modified_at' => null];
        }

        return [
            'available' => true,
            'size_bytes' => filesize($path),
            'modified_at' => Carbon::createFromTimestamp(filemtime($path))->toJSON(),
        ];
    }
}
