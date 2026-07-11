<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['name' => 'superadmin',     'display_name' => 'Super Administrador',      'description' => 'Control total del sistema EcoSort. Gestiona usuarios, roles y configuración global.',                         'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'moderador',      'display_name' => 'Moderador',                'description' => 'Administrador EcoSort con acceso amplio. No puede gestionar otros moderadores ni al superadmin.',              'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'admin_merchant', 'display_name' => 'Administrador de Comercio','description' => 'Administra una alianza/comercio específico. Puede crear y aprobar recompensas dentro de su organización.',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'merchant',       'display_name' => 'Comerciante',              'description' => 'Empleado de un comercio aliado. Se encarga de procesar canjes de recompensas presenciales.',                   'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'member',         'display_name' => 'Miembro / Usuario Final',  'description' => 'Usuario final de la app. Puede o no pertenecer a una organización aliada.',                                    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
