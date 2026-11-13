<?php

namespace Tests\Feature\Nearby;

use App\Enums\AllianceStatus;
use App\Enums\RewardStatus;
use App\Models\OrganizationMember;
use App\Models\User;

class NearbyShopsTest extends NearbyTestCase
{
    private function nearby(User $user, array $body)
    {
        return $this->actingAs($user)->postJson('/api/nearby/shops', $body);
    }

    private function ids($response): array
    {
        return array_column($response->json('data.shops'), 'id');
    }

    public function test_comercios_del_radio_con_recompensas_por_distancia(): void
    {
        $lejos = $this->shop(0.027);   // ~3 km
        $cerca = $this->shop(0.009);   // ~1 km
        $fuera = $this->shop(0.09);    // ~10 km
        $sinCoordenadas = $this->shop(null);
        $pausado = $this->shop(0.009, ['status' => AllianceStatus::PAUSED]);
        $sinDisponibles = $this->shop(0.009);

        foreach ([$lejos, $cerca, $fuera, $sinCoordenadas, $pausado] as $shop) {
            $this->reward($shop, 10);
        }
        $this->reward($cerca, 500);
        $this->reward($sinDisponibles, 10, ['status' => RewardStatus::PENDING]);
        $this->reward($sinDisponibles, 10, ['stock' => 0]);
        $this->reward($sinDisponibles, 10, ['expires_at' => now()->subDay()]);

        $response = $this->nearby($this->user(100), $this->location())
            ->assertOk()
            ->assertJsonPath('data.total_points', 100)
            ->assertJsonPath('data.shops.0.available_rewards_count', 2)
            ->assertJsonPath('data.shops.0.redeemable_rewards_count', 1)
            ->assertJsonPath('data.shops.1.available_rewards_count', 1)
            ->assertJsonMissingPath('data.shops.0.contact_email')
            ->assertJsonMissingPath('data.shops.0.phone');

        $this->assertSame([$cerca->id, $lejos->id], $this->ids($response));
        $this->assertEqualsWithDelta(1000, $response->json('data.shops.0.distance_m'), 10);
        $this->assertSame('Cafetería', $response->json('data.shops.0.type_shop.name'));
        $this->assertSame('coffee', $response->json('data.shops.0.type_shop.icon'));
        $this->assertStringEndsWith('/icons/type-shop/coffee.svg', $response->json('data.shops.0.type_shop.icon_url'));
    }

    public function test_comercio_con_solo_exclusivas_sale_solo_a_sus_miembros(): void
    {
        $exclusivo = $this->shop(0.009, ['has_exclusive_rewards' => true]);
        $this->reward($exclusivo, 10, ['is_exclusive' => true]);

        $this->assertSame([], $this->ids($this->nearby($this->user(), $this->location())->assertOk()));

        $member = $this->user();
        OrganizationMember::create(['user_id' => $member->id, 'alliance_id' => $exclusivo->id]);

        $this->nearby($member, $this->location())
            ->assertOk()
            ->assertJsonPath('data.shops.0.id', $exclusivo->id)
            ->assertJsonPath('data.shops.0.available_rewards_count', 1)
            ->assertJsonPath('data.shops.0.redeemable_rewards_count', 0);
    }

    public function test_sin_ubicacion_o_ubicacion_invalida_es_422(): void
    {
        $user = $this->user();

        $this->nearby($user, [])->assertStatus(422)->assertJsonValidationErrors(['latitude', 'longitude', 'accuracy', 'captured_at']);
        $this->nearby($user, $this->location(['accuracy' => 800]))->assertStatus(422)->assertJsonValidationErrors('accuracy');
        $this->nearby($user, $this->location(['captured_at' => now()->subMinutes(30)->toIso8601String()]))->assertStatus(422)->assertJsonValidationErrors('captured_at');
        $this->nearby($user, $this->location(['captured_at' => now()->addHour()->toIso8601String()]))->assertStatus(422)->assertJsonValidationErrors('captured_at');
    }

    public function test_limit_por_defecto_5_y_topado_a_50(): void
    {
        collect(range(1, 55))->each(fn () => $this->reward($this->shop(0.009), 10));
        $user = $this->user();

        $this->assertCount(5, $this->ids($this->nearby($user, $this->location())->assertOk()));
        $this->assertCount(3, $this->ids($this->nearby($user, $this->location(['limit' => 3]))->assertOk()));
        $this->assertCount(50, $this->ids($this->nearby($user, $this->location(['limit' => 1000]))->assertOk()));
    }
}
