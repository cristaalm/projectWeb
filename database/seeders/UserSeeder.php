<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'verification_status' => VerificationStatus::APPROVED->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        // Usuarios normales
        User::factory(50)
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
