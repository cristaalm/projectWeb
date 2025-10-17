<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory;

class SetPhoneUsers extends Seeder
{
    public function run()
    {
        $faker = Factory::create();

        User::chunkById(200, function ($users) use ($faker) {
                foreach ($users as $user) {
                    // Generar un número de 10 dígitos (por ejemplo, números mexicanos o genéricos)
                    $phone = $faker->numerify('##########'); // 10 dígitos

                    $user->phone = $phone;
                    $user->save();
                }
            });
    }
}
