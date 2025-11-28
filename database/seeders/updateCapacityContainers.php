<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Container;

class updateCapacityContainers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // obtenemos todos los contenedores
        $containers = Container::all();

        // recorremos todos los contenedores
        foreach ($containers as $container) {
            // actualizamos los sensores de forma aleatoria
            $container->capacity = [
                'sensor1' => rand(0, 100),
                'sensor2' => rand(0, 100),
                'sensor3' => rand(0, 100),
            ];
            $container->save();
        }

        // actualizamos el contenedor con id 1
        $container = Container::find(1);
        $container->capacity = [
            'sensor1' => rand(0, 100),
            'sensor2' => rand(0, 100),
            'sensor3' => rand(0, 100),
        ];
        $container->save();
    }
}
