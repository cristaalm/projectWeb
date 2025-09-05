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
            'display_name' => 'Usuario solicitante',
            'description' => 'Usuario regular del sistema',
            'is_active' => true,
        ]);

        Role::create([
            'name' => 'admin',
            'display_name' => 'Administrador general',
            'description' => 'Tiene acceso total al sistema',
            'is_active' => true,
        ]);
    }
}
