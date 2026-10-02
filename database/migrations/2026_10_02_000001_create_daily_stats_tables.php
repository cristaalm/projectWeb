<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tablas de resumen diario que alimentan el panel principal. Las llena
 * `php artisan dashboard:aggregate-daily` con los días ya cerrados; el día
 * en curso nunca se guarda aquí (el panel lo calcula en vivo).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_scan_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('container_id');
            $table->unsignedBigInteger('material_type_id');
            $table->integer('valid_scans')->default(0);
            $table->integer('failed_scans')->default(0);
            $table->integer('points_awarded')->default(0);

            // Mismo cascade que scans: si se elimina el contenedor o el
            // material, sus escaneos desaparecen y el resumen debe cuadrar.
            $table->foreign('container_id', 'fk_daily_scan_stats_container')
                ->references('id')->on('containers')->onDelete('cascade');

            $table->foreign('material_type_id', 'fk_daily_scan_stats_material')
                ->references('id')->on('material_types')->onDelete('cascade');

            $table->unique(['date', 'container_id', 'material_type_id'], 'uq_daily_scan_stats');
        });

        Schema::create('daily_user_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('user_id');
            $table->integer('valid_scans')->default(0);
            $table->integer('scan_points')->default(0);
            $table->integer('badge_points')->default(0);

            $table->foreign('user_id', 'fk_daily_user_stats_user')
                ->references('id')->on('users')->onDelete('cascade');

            $table->unique(['date', 'user_id'], 'uq_daily_user_stats');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_user_stats');
        Schema::dropIfExists('daily_scan_stats');
    }
};
