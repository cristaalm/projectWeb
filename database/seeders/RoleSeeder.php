<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name' => 'user',
            'display_name' => 'Usuario',
            'description' => 'Usuario regular del sistema',
            'is_active' => true,
        ]);

        Role::create([
            'name' => 'admin',
            'display_name' => 'Administrador',
            'description' => 'Tiene acceso total al sistema',
            'is_active' => true,
        ]);

        Role::create([
            'name' => 'moderator',
            'display_name' => 'Moderador',
            'description' => 'Tiene acceso limitado al sistema',
            'is_active' => true,
        ]);

        Role::create([
            'name' => 'comerciante',
            'display_name' => 'Comerciante',
            'description' => 'Comerciante del sistema',
            'is_active' => true,
        ]);
    }
}
