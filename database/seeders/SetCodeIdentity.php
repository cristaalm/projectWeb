<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory;

class SetCodeIdentity extends Seeder
{
    public function run()
    {
        $faker = Factory::create();
        $usedCodes = [];

        foreach (User::cursor() as $user) {
            // Si ya tiene un código, puedes saltarlo o regenerarlo
            if (!empty($user->code_identity)) {
                continue;
            }

            $code = null;
            do {
                // Generar 12 dígitos aleatorios
                $digits12 = implode('', $faker->randomElements(range('0', '9'), 12, true));
                // Calcular el dígito de control
                $checkDigit = User::calculateEan13CheckDigit($digits12);
                $code = $digits12 . $checkDigit;
            } while (in_array($code, $usedCodes, true));

            $usedCodes[] = $code;
            $user->code_identity = $code;
            $user->save();
        }
    }
}
