<?php

namespace Database\Factories;

use App\Models\Reward;
use App\Models\Alliance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RewardFactory extends Factory
{
    protected $model = Reward::class;

    public function definition()
    {
        $names = [
            'Auriculares Gamer RGB',
            'Teclado Mecánico Retroiluminado',
            'Mouse Inalámbrico de Alta Precisión',
            'Tarjeta de Regalo Steam ($20)',
            'Suscripción Xbox Game Pass (1 mes)',
            'Power Bank 20,000 mAh',
            'SSD 1TB NVMe',
            'Monitor 144Hz 24"',
            'Control DualSense (PS5)',
            'Cargador Rápido USB-C',
            'Vale Amazon de $25',
            'Cupón Walmart $30',
            'Tarjeta de Regalo Nike',
            'Vale de Descuento Apple Store',
            'Cupón de Regalo Starbucks',
            'Vale de $50 en Best Buy',
            'Tarjeta de Regalo de Netflix (3 meses)',
            'Vale de Descuento en Zara',
            'Cupón de Regalo en Uber Eats',
            'Vale de $40 en Target',
            'Mochila Antirrobo para Laptop',
            'Botella Térmica Inteligente',
            'Reloj Inteligente Básico',
            'Set de Cargadores Multidispositivo',
            'Parlante Bluetooth Portátil',
            'Lámpara LED RGB con Control por App',
            'Funda para Laptop 15.6"',
            'Organizador de Escritorio',
            'Taza Inteligente con Display',
            'Kit de Limpieza para Pantallas',
            'Cena para Dos en Restaurante Premium',
            'Voucher de Delivery Gourmet',
            'Caja Sorpresa de Snacks Internacionales',
            'Suscripción Mensual de Café Artesanal',
            'Vale para Heladería Premium',
            'Kit de Cocina Gourmet (3 recetas)',
            'Degustación de Vinos (2 botellas)',
            'Vale de Desayuno en Hotel',
            'Caja de Chocolates Artesanales',
            'Menú Ejecutivo Semanal (5 días)',
            'Entradas al Cine (2 personas)',
            'Pase Anual a Parque Temático',
            'Libro Bestseller del Momento',
            'Suscripción a Spotify Premium (6 meses)',
            'Entradas a Concierto Local',
            'Vale para Museo de Arte',
            'Juego de Mesa Estratégico',
            'Suscripción a Disney+ (3 meses)',
            'Kit de Pintura por Números',
            'Rompecabezas 1000 piezas',
            'Sesión de Masaje Relajante',
            'Kit de Aromaterapia',
            'Suscripción a App de Meditación',
            'Colchoneta de Yoga Premium',
            'Set de Tés Orgánicos',
            'Difusor de Aceites Esenciales',
            'Vale para Spa de Lujo',
            'Kit de Cuidado Facial Natural',
            'Botella de Agua con Filtro',
            'Diario de Gratitud Personalizado',
            'Curso Online en Udemy',
            'Suscripción a LinkedIn Learning',
            'Bloc de Notas Inteligente Reutilizable',
            'Lápiz Digital para Tabletas',
            'Organizador Semanal Impreso',
            'Vale para Librería Técnica',
            'Kit de Productividad (planner + stickers)',
            'Suscripción a Notion Pro',
            'Curso de Idiomas (1 mes)',
            'Set de Marcadores para Estudio',
            'Juguete Educativo STEM',
            'Kit de Manualidades Infantiles',
            'Cuenta de Disney+ Familiar',
            'Libro Ilustrado para Niños',
            'Vale para Parque de Atracciones',
            'Mochila Escolar con Ruedas',
            'Juego Didáctico de Matemáticas',
            'Kit de Ciencia para Niños',
            'Peluche Personalizado',
            'Bolsa Reutilizable de Algodón Orgánico',
            'Set de Cubiertos de Bambú',
            'Botella de Vidrio con Infusor',
            'Jabón Artesanal Zero Waste',
            'Kit de Huerto Urbano',
            'Bolsas de Compras Biodegradables',
            'Velas de Cera de Soja',
            'Set de Cepillos de Bambú',
            'Contenedor de Alimentos de Acero',
            'Kit de Limpieza Ecológica',
            'Llavero Personalizado con NFC',
            'Imán de Nevera Personalizado',
            'Tarjeta de Felicitación Artesanal',
            'Set de Pegatinas Premium',
            'Marcador de Libros de Metal',
            'Portarretrato Digital Mini',
            'Kit de Origami Avanzado',
            'Puzzle 3D de Monumentos',
            'Reloj de Arena Decorativo',
            'Caja de Mensajes Positivos' 
        ];

        $digits12 = implode('', $this->faker->randomElements(range('0', '9'), 12, true));
        $checkDigit = Reward::calculateEan13CheckDigit($digits12);

        return [
            'name' => $this->faker->randomElement($names),
            'description' => $this->faker->sentence(),
            'points_required' => $this->faker->numberBetween(100, 10000),
            'stock' => $this->faker->numberBetween(1, 100),
            'code' => $checkDigit,
            'is_active' => $this->faker->boolean(80), // 80% activo
            'expires_at' => $this->faker->optional(0.3)->dateTimeBetween('+1 week', '+6 months'),
            'alliance_id' => Alliance::factory(),
            'image' => false, // o true si quieres simular imágenes
        ];
    }
}
