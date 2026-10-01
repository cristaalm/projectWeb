<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * El contenedor ahora clasifica el material en el borde y reporta el
 * resultado a la API: la imagen pasa a ser opcional, y `event_id` (generado
 * por el contenedor, uno por escaneo) evita que un reintento del dispositivo
 * acredite dos veces el mismo reciclaje.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->uuid('event_id')->nullable()->after('id');
            $table->unique('event_id', 'uq_scans_event_id');
        });

        // Mismo criterio que widen_users_google2fa_secret_column: SQL crudo
        // específico de Postgres en vez de Blueprint::change().
        DB::statement('ALTER TABLE scans ALTER COLUMN image DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement("UPDATE scans SET image = '' WHERE image IS NULL");
        DB::statement('ALTER TABLE scans ALTER COLUMN image SET NOT NULL');

        Schema::table('scans', function (Blueprint $table) {
            $table->dropUnique('uq_scans_event_id');
            $table->dropColumn('event_id');
        });
    }
};
