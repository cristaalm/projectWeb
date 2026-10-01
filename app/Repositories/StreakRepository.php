<?php

namespace App\Repositories;

use App\Models\UserStreak;

class StreakRepository
{
    public function findByUser(int $userId): ?UserStreak
    {
        return UserStreak::where('user_id', $userId)->first();
    }

    public function save(int $userId, array $attributes): UserStreak
    {
        return UserStreak::updateOrCreate(['user_id' => $userId], $attributes);
    }
}
