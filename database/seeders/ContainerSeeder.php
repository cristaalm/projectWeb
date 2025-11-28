<?php

namespace Database\Seeders;

use App\Models\Container;
use Illuminate\Database\Seeder;
use App\Enums\ContainerStatus;

class ContainerSeeder extends Seeder
{
    public function run(): void
    {

        $containers = [
            ["name" => "Parque central de Manzanillo", "status" => ContainerStatus::ACTIVE, "address" => "Av. Morelos 114-126, Valle Dorado, 28200 Manzanillo, Col."],
            ["name" => "Jardín de Santiago", "status" => ContainerStatus::ACTIVE, "address" => "Santiago Centro, 28860 Manzanillo, Col."],
            ["name" => "Jardín de la joya 1", "status" => ContainerStatus::ACTIVE, "address" => "La Joyas, La Joya, 28864 Manzanillo, Col."],
            ["name" => "Facultad de Ingeniería Electromecánica (FIE)", "status" => ContainerStatus::ACTIVE, "address" => "Carretera Manzanillo-Cihuatlan Km. 20, 28860 Manzanillo, Col."],
            ["name" => "Preparatoria técnica CETIs 84", "status" => ContainerStatus::ACTIVE, "address" => "Concha Nacar 148, La Joya II, 28864 Manzanillo, Col."],
            ["name" => "Facultad de Contabilidad y Administración de Manzanillo (FCAM)", "status" => ContainerStatus::ACTIVE, "address" => "Carretera Manzanillo-Cihuatlán kilómetro 20, México, 28860 Manzanillo, Col., El naranjo, 28870 Manzanillo, Col."],
            ["name" => "Facultad de Ciencias Marinas (FACIMAR)", "status" => ContainerStatus::ACTIVE, "address" => "Universidad de Colima, Carretera Manzanillo - Barra de Navidad, El Naranjo, 28868 Manzanillo, Col."],
            ["name" => "Jardín de la Miguel Hidalgo", "status" => ContainerStatus::ACTIVE, "address" => "C. 1, Padre Hidalgo, 28270 Manzanillo, Col."],
            ["name" => "Plaza Manzanillo (La comer)", "status" => ContainerStatus::ACTIVE, "address" => "Blvd. Miguel de la Madrid 1337, Salagua, 28867 Manzanillo, Col."],
            ["name" => "Soriana - Manzanillo", "status" => ContainerStatus::ACTIVE, "address" => "Blvd. Miguel de la Madrid 1580, Soleares, Valle de Las Garzas, 28869 Manzanillo, Col."],
            ["name" => "Universidad Tecnológica de Manzanillo (UTEM)", "status" => ContainerStatus::INACTIVE, "address" => "Camino hacia las humedades S/N, Salagua, 28869 Manzanillo, Col."],
            ["name" => "Jardín Público Palmares", "status" => ContainerStatus::INACTIVE, "address" => "Enrique González 503, 28869 Manzanillo, Col."],
            ["name" => "Bodega Aurrera, Manzanillo", "status" => ContainerStatus::INACTIVE, "address" => "Elias Zamora Verduzco FR-6, Av Elías Zamora, I, 28219 Manzanillo, Col."],
            ["name" => "Jardín valle de las gaviotas", "status" => ContainerStatus::INACTIVE, "address" => "Av. de los pelícanos, Gaviotas, V, 28219 Manzanillo, Col."],
            ["name" => "Plaza Lauret", "status" => ContainerStatus::INACTIVE, "address" => "Av Elías Zamora 2114 - A, Tabachines, V, 28219 Manzanillo, Col."],
            ["name" => "jardin de colomos", "status" => ContainerStatus::INACTIVE, "address" => "El Colomo Centro, 28800 El Colomo, Col."],
            ["name" => "Jardín publico de campos", "status" => ContainerStatus::INACTIVE, "address" => "Campos, 28809 Manzanillo, Col."],
            ["name" => "Unidad Deportiva Jaime \"Tubo\" Gomez", "status" => ContainerStatus::INACTIVE, "address" => "Av Elías Zamora 352, Valle de Las Garzas, III, 28219 Manzanillo, Col."],
            ["name" => "Canchas públicas de Marimar", "status" => ContainerStatus::INACTIVE, "address" => "Calle Escalara, 28869 Manzanillo, Col."],
            ["name" => "Playa la audiencia", "status" => ContainerStatus::INACTIVE, "address" => "Sin referencia 28, Península de Santiago, 28867 Manzanillo, Col."]
        ];

        foreach ($containers as $container) {
                Container::factory()->create([
                    'name' => $container['name'],
                    'status' => $container['status'],
                    'location' => $container['address'],
                ]);
        }
    }
}
