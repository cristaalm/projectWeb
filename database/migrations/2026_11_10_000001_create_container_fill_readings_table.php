<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Historial de lecturas de llenado de los contenedores, solo de inserción
 * (nunca se edita). `container_sensors` guarda únicamente la última lectura
 * de cada compartimento; aquí queda cada lectura recibida, incluidas las que
 * llegan fuera de orden y no actualizan el estado actual, para auditoría.
 *
 * La purga por antigüedad la hace `php artisan containers:prune-fill-readings`
 * (scheduler), usando `created_at` — la hora del servidor, que no depende del
 * reloj del contenedor.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('container_fill_readings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('container_id');
            $table->string('sensor_key', 50);
            $table->unsignedBigInteger('material_type_id')->nullable();
            $table->decimal('fill_level', 5, 2);
            $table->timestamp('measured_at');
            $table->smallInteger('source');
            $table->unsignedBigInteger('scan_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Mismo comportamiento que container_sensors: si se elimina el
            // contenedor se va su historial; si se elimina el material o el
            // escaneo, la lectura se conserva sin esa referencia.
            $table->foreign('container_id', 'fk_fill_readings_container')
                ->references('id')->on('containers')->onDelete('cascade');

            $table->foreign('material_type_id', 'fk_fill_readings_material')
                ->references('id')->on('material_types')->onDelete('set null');

            $table->foreign('scan_id', 'fk_fill_readings_scan')
                ->references('id')->on('scans')->onDelete('set null');

            $table->index(['container_id', 'measured_at'], 'idx_fill_readings_container_measured');
            $table->index('created_at', 'idx_fill_readings_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('container_fill_readings');
    }
};
