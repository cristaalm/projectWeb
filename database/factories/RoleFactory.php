<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $names = ['user', 'admin', 'moderator', 'comerciante'];
        $displayNames = [
            'user' => 'Usuario',
            'admin' => 'Administrador',
            'moderator' => 'Moderador',
            'comerciante' => 'Comerciante',
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
