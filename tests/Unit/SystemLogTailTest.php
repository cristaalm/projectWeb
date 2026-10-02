<?php

namespace Tests\Unit;

use App\Exceptions\SystemLogException;
use App\Services\SystemLogService;
use PHPUnit\Framework\TestCase;

/**
 * Lectura del final de un log (sin base de datos ni framework): solo
 * SystemLogService::tail() contra archivos temporales.
 */
class SystemLogTailTest extends TestCase
{
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    private function file(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'log');
        file_put_contents($path, $content);

        return $this->files[] = $path;
    }

    private function numbered(int $count): string
    {
        return implode("\n", array_map(fn ($n) => "linea {$n}", range(1, $count)))."\n";
    }

    public function test_returns_the_last_lines_in_chronological_order(): void
    {
        $tail = SystemLogService::tail($this->file($this->numbered(50)), 3);

        $this->assertSame(['linea 48', 'linea 49', 'linea 50'], $tail['lines']);
        $this->assertFalse($tail['reached_start']);
    }

    public function test_a_short_file_is_returned_whole_and_reports_its_start(): void
    {
        $tail = SystemLogService::tail($this->file("uno\ndos\ntres"), 10);

        $this->assertSame(['uno', 'dos', 'tres'], $tail['lines']);
        $this->assertTrue($tail['reached_start']);
    }

    public function test_lines_split_across_read_blocks_are_reassembled(): void
    {
        // Líneas más largas que el bloque de lectura (8 KB), para que cada
        // una quede partida en varios bloques.
        $long = fn (string $char) => str_repeat($char, 20000);
        $tail = SystemLogService::tail($this->file($long('a')."\n".$long('b')."\n".$long('c')."\n"), 2);

        $this->assertSame([$long('b'), $long('c')], $tail['lines']);
    }

    public function test_blank_lines_and_windows_line_endings_are_ignored(): void
    {
        $tail = SystemLogService::tail($this->file("uno\r\n\r\n\ndos\r\n"), 10);

        $this->assertSame(['uno', 'dos'], $tail['lines']);
    }

    public function test_search_keeps_only_matching_lines_ignoring_case(): void
    {
        $content = "Running [artisan inspire]\nNo scheduled commands\nRUNNING [artisan dashboard:aggregate-daily]\nNo scheduled commands\n";
        $tail = SystemLogService::tail($this->file($content), 10, 'running');

        $this->assertSame(['Running [artisan inspire]', 'RUNNING [artisan dashboard:aggregate-daily]'], $tail['lines']);
        $this->assertTrue($tail['reached_start']);
    }

    public function test_search_looks_further_back_than_the_requested_number_of_lines(): void
    {
        $content = "aguja\n".$this->numbered(500);
        $tail = SystemLogService::tail($this->file($content), 1, 'aguja');

        $this->assertSame(['aguja'], $tail['lines']);
    }

    public function test_scanning_stops_at_the_byte_budget(): void
    {
        $tail = SystemLogService::tail($this->file($this->numbered(1000)), 1000, null, 100);

        $this->assertSame(100, $tail['scanned_bytes']);
        $this->assertFalse($tail['reached_start']);
        $this->assertSame('linea 1000', end($tail['lines']));
        $this->assertLessThan(15, count($tail['lines']));
        // La primera línea del tramo leído puede venir cortada: no se devuelve.
        $this->assertMatchesRegularExpression('/^linea \d{3,4}$/', $tail['lines'][0]);
    }

    public function test_invalid_utf8_bytes_do_not_break_the_output(): void
    {
        $tail = SystemLogService::tail($this->file("bien\nmal \xC3\x28 byte\n"), 10);

        $this->assertCount(2, $tail['lines']);
        $this->assertNotFalse(json_encode($tail['lines']));
    }

    public function test_an_empty_file_returns_no_lines(): void
    {
        $tail = SystemLogService::tail($this->file(''), 10);

        $this->assertSame([], $tail['lines']);
        $this->assertTrue($tail['reached_start']);
    }

    public function test_a_missing_file_is_reported_as_a_domain_error(): void
    {
        $this->expectException(SystemLogException::class);

        SystemLogService::tail('/no/existe.log', 10);
    }
}
