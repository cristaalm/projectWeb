<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $names = ['user', 'admin', 'moderator'];
        $displayNames = [
            'user' => 'Usuario solicitante',
            'admin' => 'Administrador general',
            'moderator' => 'Moderador de contenido',
        ];

        $name = $this->faker->unique()->randomElement($names);

        return [
            'name' => $name,
            'display_name' => $displayNames[$name],
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
