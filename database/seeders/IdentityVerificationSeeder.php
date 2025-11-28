<?php

namespace Database\Seeders;

use App\Models\IdentityVerification;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\VerificationStatus;

class IdentityVerificationSeeder extends Seeder
{
    public function run(): void
    {
        // obtenemos los usuari     os que no han sido verificados
        $users = User::all();

        foreach ($users as $user) {
            IdentityVerification::factory()
                ->forUser($user)
                ->create();
        }
    }
}
