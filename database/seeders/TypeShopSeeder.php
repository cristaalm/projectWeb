<?php

namespace Database\Seeders;

use App\Models\TypeShop;
use Illuminate\Database\Seeder;

class TypeShopSeeder extends Seeder
{
    public function run(): void
    {
        // Nombre => icono del catálogo App\Support\TypeShopIcons.
        $typeShops = [
            'Supermercado' => 'cart',
            'Farmacia' => 'pill',
            'Restaurant' => 'silverware-fork-knife',
            'Tienda' => 'store',
            'Gasolinera' => 'gas-station',
            'Bodega' => 'warehouse',
            'Tienda de electrónica' => 'laptop',
            'Tienda de libros' => 'book-open-page-variant',
            'Tienda de juguetes' => 'teddy-bear',
            'Tienda de ropa' => 'tshirt-crew',
        ];

        foreach ($typeShops as $name => $icon) {
            TypeShop::factory()->create([
                'name' => $name,
                'icon' => $icon,
            ]);
        }
    }
}
