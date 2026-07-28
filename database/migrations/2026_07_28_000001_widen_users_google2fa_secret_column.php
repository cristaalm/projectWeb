<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * El cast `encrypted` en User::google2fa_secret produce valores mucho más
 * largos que el secreto TOTP en texto plano — VARCHAR(100) se queda corto.
 * Sin doctrine/dbal instalado, Blueprint::change() no está disponible, así
 * que se usa SQL crudo específico de Postgres.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE users ALTER COLUMN google2fa_secret TYPE TEXT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users ALTER COLUMN google2fa_secret TYPE VARCHAR(100)');
    }
};
