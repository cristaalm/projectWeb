<?php

use App\Support\TypeShopIcons;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Icono por categoría, tomado del catálogo cerrado App\Support\TypeShopIcons
     * (nombre de Material Design Icons). Las categorías existentes quedan con el
     * icono genérico, salvo las del seeder, que reciben uno acorde a su nombre.
     */
    public function up(): void
    {
        Schema::table('type_shop', function (Blueprint $table) {
            $table->string('icon', 60)->default(TypeShopIcons::DEFAULT)->after('name');
        });

        $byName = [
            'supermercado' => 'cart',
            'farmacia' => 'pill',
            'restaurant' => 'silverware-fork-knife',
            'restaurante' => 'silverware-fork-knife',
            'tienda' => 'store',
            'gasolinera' => 'gas-station',
            'bodega' => 'warehouse',
            'tienda de electrónica' => 'laptop',
            'tienda de libros' => 'book-open-page-variant',
            'tienda de juguetes' => 'teddy-bear',
            'tienda de ropa' => 'tshirt-crew',
            'cafetería' => 'coffee',
            'panadería' => 'bread-slice',
        ];

        foreach ($byName as $name => $icon) {
            DB::table('type_shop')->whereRaw('LOWER(name) = ?', [$name])->update(['icon' => $icon]);
        }
    }

    public function down(): void
    {
        Schema::table('type_shop', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
