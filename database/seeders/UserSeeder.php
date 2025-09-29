<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use PragmaRX\Google2FA\Google2FA;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        $adminRole = Role::firstWhere('name', 'admin') ??
            Role::create([
                'name' => 'admin',
                'display_name' => 'Administrador general',
                'description' => 'Acceso total al sistema',
                'is_active' => true,
            ]);

        // Admin
        User::factory()->create([
            'name' => 'Renova',
            'last_name' => 'app',
            'email' => 'soyrenovaapp@gmail.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
            'google2fa_secret' => '5LVIDG4DFV23RFSE',
            'two_factor_status' => 1,
            'verification_status' => VerificationStatus::APPROVED->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $usersData = [
            [
                'name' => 'Eduardo',
                'last_name' => 'Arcega Rodríguez',
                'email' => 'earcega@ucol.mx',
                'two_factor_status' => 1,
                'google2fa_secret' => 'U7OJP6532ABBD3G4',
            ],
            [
                'name' => 'Annelise Najara',
                'last_name' => 'Cabrales López',
                'email' => 'acabrales@ucol.mx',
                'two_factor_status' => 0,
                'google2fa_secret' => 'NNGOO3XNG5MGLIKM',
            ],
            [
                'name' => 'Antonio Jesús',
                'last_name' => 'Enríquez Tinoco',
                'email' => 'jenriquez0@ucol.mx',
                'two_factor_status' => 0,
                'google2fa_secret' => '42VPVMIIIZCJR2PS',
            ],
            [
                'name' => 'Victor Josué',
                'last_name' => 'Larios Rosas',
                'email' => 'vlarios10@ucol.mx',
                'two_factor_status' => 0,
                'google2fa_secret' => 'JI6J7VLK4JEGOFXF',
            ],
            [
                'name' => 'Brisa Cristal',
                'last_name' => 'Medina López',
                'email' => 'bmedina1@ucol.mx',
                'two_factor_status' => 0,
                'google2fa_secret' => '6ZWM3SPSEWQL4M5Z',
            ],
            [
                'name' => 'Jesús Guadalupe',
                'last_name' => 'Rivera Meza',
                'email' => 'jrivera7@ucol.mx',
                'two_factor_status' => 0,
                'google2fa_secret' => '5T3O6QWXQ52B7A53',
            ],
        ];

        foreach ($usersData as $userData) {
            User::factory()->create([
                'name' => $userData['name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'password' => Hash::make('admin123'), // Contraseña común para todos (puedes cambiarla si lo deseas)
                'role_id' => $adminRole->id,
                'email_verified_at' => Carbon::now(),
                'google2fa_secret' => $userData['google2fa_secret'],
                'two_factor_status' => $userData['two_factor_status'],
                'verification_status' => VerificationStatus::APPROVED->value,
                'status' => UserStatus::ACTIVE->value,
            ]);

            $this->command->info("Usuario creado: {$userData['email']}");
        }

        // Usuarios normales
        User::factory(100)
            ->state([
                'status' => UserStatus::ACTIVE->value,
                'verification_status' => fake()->randomElement([
                    VerificationStatus::PENDING->value,
                    VerificationStatus::APPROVED->value,
                    VerificationStatus::REJECTED->value,
                ]),
            ])
            ->create();
    }
}
