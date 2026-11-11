<?php

namespace Tests\Feature\Nearby;

use App\Enums\AllianceStatus;
use App\Enums\RewardStatus;
use App\Models\Alliance;
use App\Models\Reward;
use App\Models\Role;
use App\Models\TypeShop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Datos de prueba compartidos por los tests de los endpoints "cerca de ti".
 */
abstract class NearbyTestCase extends TestCase
{
    use RefreshDatabase;

    // Centro de Colima; +0.009° de latitud ≈ 1 km.
    protected const LAT = 19.2433;

    protected const LNG = -103.7250;

    protected TypeShop $typeShop;

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeShop = TypeShop::create(['name' => 'Cafetería', 'is_active' => true]);
    }

    protected function user(int $points = 0): User
    {
        $digits12 = str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
        $user = User::create([
            'name' => 'Test',
            'last_name' => 'User',
            'email' => 'test-'.uniqid().'@example.com',
            'phone' => '5550000000',
            'password' => 'password',
            'code_identity' => $digits12.User::calculateEan13CheckDigit($digits12),
            'role_id' => Role::where('name', 'member')->firstOrFail()->id,
        ]);

        if ($points) {
            DB::table('point_adjustments')->insert([
                'user_id' => $user->id, 'admin_user_id' => $user->id, 'points' => $points, 'reason' => 'Prueba', 'created_at' => now(),
            ]);
        }

        return $user;
    }

    protected function shop(?float $latOffset, array $attributes = []): Alliance
    {
        return Alliance::create([
            'type_shop_id' => $this->typeShop->id,
            'name' => 'Comercio '.Str::random(4),
            'contact_name' => 'Contacto',
            'contact_email' => 'contacto@example.com',
            'phone' => '5550000000',
            'address' => 'Calle 1',
            'latitude' => $latOffset === null ? null : self::LAT + $latOffset,
            'longitude' => $latOffset === null ? null : self::LNG,
            'status' => AllianceStatus::ACTIVE,
            ...$attributes,
        ]);
    }

    protected function reward(Alliance $shop, int $points, array $attributes = []): Reward
    {
        return Reward::create([
            'alliance_id' => $shop->id,
            'name' => "Recompensa {$points}",
            'points_required' => $points,
            'code' => Str::random(12),
            'status' => RewardStatus::APPROVED,
            ...$attributes,
        ]);
    }

    protected function location(array $overrides = []): array
    {
        return ['latitude' => self::LAT, 'longitude' => self::LNG, 'accuracy' => 12.5, 'captured_at' => now()->toIso8601String(), ...$overrides];
    }
}
