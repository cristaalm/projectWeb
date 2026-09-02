<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Un hash no se puede diferenciar post-hoc de otro — esta columna es la única
 * forma confiable de saber si el usuario conoce una contraseña real o si la
 * suya es la aleatoria autogenerada en signup social
 * (AuthService::createSocialUser).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_usable_password')->default(true)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_usable_password');
        });
    }
};
