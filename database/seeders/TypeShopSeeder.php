<?php

namespace Database\Seeders;

use App\Models\TypeShop;
use Illuminate\Database\Seeder;

class TypeShopSeeder extends Seeder
{
    public function run(): void
    {

        $typeShops = [
            'Supermercado',
            'Farmacia',
            'Restaurant',
            'Tienda',
            'Gasolinera',
            'Bodega',
            'Tienda de electrónica',
            'Tienda de libros',
            'Tienda de juguetes',
            'Tienda de ropa',
        ];

        foreach ($typeShops as $typeShop) {
            TypeShop::factory()->create([
                'name' => $typeShop,
            ]);
        }
    }
}
