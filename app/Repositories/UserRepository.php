<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    public function phoneExists(string $phone): bool
    {
        return User::where('phone', $phone)->exists();
    }

    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    public function defaultRegistrationRole(): ?Role
    {
        return Role::firstWhere('name', 'member');
    }
}
