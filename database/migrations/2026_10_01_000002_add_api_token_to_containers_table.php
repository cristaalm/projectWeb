<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Cada contenedor se autentica contra la API con su propio token, en vez de
 * una API key compartida. `api_token` va cifrado (el panel administrativo
 * necesita poder mostrarlo) y `api_token_hash` es su SHA-256, indexado, para
 * resolver el contenedor sin descifrar toda la tabla en cada petición.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            $table->text('api_token')->nullable()->after('status');
            $table->string('api_token_hash', 64)->nullable()->after('api_token');
            $table->unique('api_token_hash', 'uq_containers_api_token_hash');
        });

        // Los contenedores que ya existían reciben su token aquí. Mismo
        // formato y cifrado que App\Models\Container (cast `encrypted`).
        DB::table('containers')->orderBy('id')->get(['id'])->each(function ($container) {
            $token = 'ect_'.Str::random(48);

            DB::table('containers')->where('id', $container->id)->update([
                'api_token' => Crypt::encryptString($token),
                'api_token_hash' => hash('sha256', $token),
            ]);
        });

        DB::statement('ALTER TABLE containers ALTER COLUMN api_token SET NOT NULL');
        DB::statement('ALTER TABLE containers ALTER COLUMN api_token_hash SET NOT NULL');
    }

    public function down(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            $table->dropUnique('uq_containers_api_token_hash');
            $table->dropColumn(['api_token', 'api_token_hash']);
        });
    }
};
