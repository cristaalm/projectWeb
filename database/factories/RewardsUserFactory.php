<?php

namespace Database\Factories;

use App\Models\RewardsUser;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RewardsUserFactory extends Factory
{
    protected $model = RewardsUser::class;

    public function definition(): array
    {
        $reward = Reward::select('id', 'single_use', 'created_at', 'expires_at')->inRandomOrder()->first();
        $user;
        $redeemed_at;
        $usuariosElejidos = [];

        do {
            $user = User::select('id')->whereNotIn('id', $usuariosElejidos)->inRandomOrder()->first();
            $usuariosElejidos[] = $user->id;
        } while ($reward->single_use && $user->rewards()->where('reward_id', $reward->id)->exists());

        if ($reward->expires_at === null) {
            $redeemed_at = $this->faker->dateTimeBetween($reward->created_at, now());
        } else {
            $redeemed_at = $this->faker->dateTimeBetween($reward->created_at, $reward->expires_at);
        }

        return [
            'reward_id' => $reward->id,
            'user_id' => $user->id,
            'redeemed_at' => $redeemed_at,
        ];
    }
}
