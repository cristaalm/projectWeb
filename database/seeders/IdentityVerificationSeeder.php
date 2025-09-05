<?php

namespace Database\Seeders;

use App\Models\IdentityVerification;
use App\Enums\IndentifyVerificationStatus;
use Illuminate\Database\Seeder;

class IdentityVerificationSeeder extends Seeder
{
    public function run(): void
    {
        IdentityVerification::factory(30)
            ->state([
                'status' => fake()->randomElement(IndentifyVerificationStatus::cases())->value,
            ])
            ->create();
    }
}
