<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;
use Faker\Factory;

class SetCodeReward extends Seeder
{
    public function run()
    {
        $faker = Factory::create();
        $usedCodes = [];

        foreach (Reward::cursor() as $reward) {
            // Si ya tiene un código, puedes saltarlo o regenerarlo
            if (!empty($reward->code)) {
                continue;
            }

            $code = null;
            do {
                // Generar 12 dígitos aleatorios
                $digits12 = implode('', $faker->randomElements(range('0', '9'), 12, true));
                // Calcular el dígito de control
                $checkDigit = Reward::calculateEan13CheckDigit($digits12);
                $code = $digits12 . $checkDigit;
            } while (in_array($code, $usedCodes, true));

            $usedCodes[] = $code;
            $reward->code = $code;
            $reward->save();
        }
    }
}
