<?php

namespace Tests\Feature\Nearby;

use App\Enums\AllianceStatus;
use App\Enums\RewardStatus;
use App\Models\OrganizationMember;
use App\Models\User;

class NearbyRewardsTest extends NearbyTestCase
{
    private function nearby(User $user, array $body = [])
    {
        return $this->actingAs($user)->postJson('/api/nearby/rewards', $body);
    }

    private function ids($response): array
    {
        return array_column($response->json('data.rewards'), 'id');
    }

    public function test_sin_ubicacion_ordena_por_puntos_y_excluye_no_disponibles(): void
    {
        $shop = $this->shop(0.009);
        $other = $this->shop(0.018);
        $cara = $this->reward($shop, 80);
        $barata = $this->reward($other, 30);
        $falta10 = $this->reward($shop, 60 + 50);
        $falta40 = $this->reward($other, 140);

        // Ninguna de estas debe salir.
        $this->reward($shop, 10, ['status' => RewardStatus::PENDING]);
        $this->reward($shop, 10, ['expires_at' => now()->subDay()]);
        $this->reward($shop, 10, ['stock' => 0]);
        $this->reward($shop, 10, ['is_exclusive' => true]);
        $this->reward($shop, 10)->delete();
        $this->reward($this->shop(0.009, ['status' => AllianceStatus::PAUSED]), 10);

        $response = $this->nearby($this->user(100))
            ->assertOk()
            ->assertJsonPath('data.location_used', false)
            ->assertJsonPath('data.total_points', 100)
            ->assertJsonPath('data.rewards.0.can_redeem', true)
            ->assertJsonPath('data.rewards.0.points_missing', 0)
            ->assertJsonPath('data.rewards.2.can_redeem', false)
            ->assertJsonPath('data.rewards.2.points_missing', 10)
            ->assertJsonPath('data.rewards.0.shop.distance_m', null)
            ->assertJsonMissingPath('data.rewards.0.code');

        // Canjeables de más a menos puntos, luego las que menos le faltan.
        $this->assertSame([$cara->id, $barata->id, $falta10->id, $falta40->id], $this->ids($response));
    }

    public function test_con_ubicacion_solo_comercios_del_radio_por_distancia(): void
    {
        $lejos = $this->shop(0.027);   // ~3 km
        $cerca = $this->shop(0.009);   // ~1 km
        $fuera = $this->shop(0.09);    // ~10 km
        $sinCoordenadas = $this->shop(null);

        $rLejos = $this->reward($lejos, 10);
        $rCerca = $this->reward($cerca, 50);
        $this->reward($fuera, 10);
        $this->reward($sinCoordenadas, 10);

        $response = $this->nearby($this->user(100), $this->location())
            ->assertOk()
            ->assertJsonPath('data.location_used', true);

        $this->assertSame([$rCerca->id, $rLejos->id], $this->ids($response));
        $this->assertEqualsWithDelta(1000, $response->json('data.rewards.0.shop.distance_m'), 10);
        $this->assertEquals(self::LAT + 0.009, $response->json('data.rewards.0.shop.latitude'));
    }

    public function test_sin_comercios_en_el_radio_ignora_la_ubicacion(): void
    {
        $reward = $this->reward($this->shop(0.09), 10);

        $this->nearby($this->user(100), $this->location())
            ->assertOk()
            ->assertJsonPath('data.location_used', false)
            ->assertJsonPath('data.rewards.0.id', $reward->id)
            ->assertJsonPath('data.rewards.0.shop.distance_m', fn ($d) => $d > 9000);
    }

    public function test_ubicacion_poco_precisa_o_vieja_se_trata_como_sin_ubicacion(): void
    {
        $this->reward($this->shop(0.009), 10);
        $user = $this->user();

        foreach ([['accuracy' => 800], ['captured_at' => now()->subMinutes(30)->toIso8601String()], ['captured_at' => now()->addHour()->toIso8601String()]] as $bad) {
            $this->nearby($user, $this->location($bad))
                ->assertOk()
                ->assertJsonPath('data.location_used', false)
                ->assertJsonPath('data.rewards.0.shop.distance_m', null);
        }
    }

    public function test_ubicacion_incompleta_o_fuera_de_rango_es_422(): void
    {
        $user = $this->user();

        $this->nearby($user, ['latitude' => self::LAT])->assertStatus(422)->assertJsonValidationErrors(['longitude', 'accuracy', 'captured_at']);
        $this->nearby($user, $this->location(['latitude' => 120]))->assertStatus(422)->assertJsonValidationErrors('latitude');
        $this->nearby($user, ['limit' => 0])->assertStatus(422)->assertJsonValidationErrors('limit');
    }

    public function test_exclusivas_de_su_alianza_encabezan_la_lista(): void
    {
        $suya = $this->shop(0.009, ['has_exclusive_rewards' => true]);
        $ajena = $this->shop(0.009, ['has_exclusive_rewards' => true]);
        $normal = $this->reward($this->shop(0.009), 10);
        $exclusiva = $this->reward($suya, 500, ['is_exclusive' => true]);
        $this->reward($ajena, 10, ['is_exclusive' => true]);

        $member = $this->user(100);
        OrganizationMember::create(['user_id' => $member->id, 'alliance_id' => $suya->id]);

        $response = $this->nearby($member)
            ->assertOk()
            ->assertJsonPath('data.rewards.0.is_exclusive', true)
            ->assertJsonPath('data.rewards.0.can_redeem', false);
        $this->assertSame([$exclusiva->id, $normal->id], $this->ids($response));

        $this->assertSame([$normal->id], $this->ids($this->nearby($this->user(100))));
    }

    public function test_maximo_dos_por_comercio_y_completa_si_no_alcanza(): void
    {
        $grande = $this->shop(0.009);
        $chico = $this->shop(0.018);
        $deGrande = collect([10, 20, 30, 40])->map(fn ($p) => $this->reward($grande, $p));
        $deChico = $this->reward($chico, 5);
        $user = $this->user(100);

        // Con límite 3: dos del comercio grande (las de más puntos) y la del chico.
        $ids = $this->ids($this->nearby($user, ['limit' => 3])->assertOk());
        $this->assertSame([$deGrande[3]->id, $deGrande[2]->id, $deChico->id], $ids);

        // Con límite 5 el tope no alcanza a llenar: se completa con el grande.
        $ids = $this->ids($this->nearby($user, ['limit' => 5])->assertOk());
        $this->assertCount(5, $ids);
        $this->assertSame([$deGrande[3]->id, $deGrande[2]->id, $deGrande[1]->id, $deGrande[0]->id, $deChico->id], $ids);
    }

    public function test_exclusivas_respetan_el_tope_y_completan_si_no_alcanza(): void
    {
        $suya = $this->shop(0.009, ['has_exclusive_rewards' => true]);
        $exclusivas = collect([10, 20, 30, 40])->map(fn ($p) => $this->reward($suya, $p, ['is_exclusive' => true]));
        $normal = $this->reward($this->shop(0.018), 5);

        $member = $this->user(100);
        OrganizationMember::create(['user_id' => $member->id, 'alliance_id' => $suya->id]);

        // Límite 3: el tope deja 2 exclusivas y entra la del otro comercio.
        $this->assertSame(
            [$exclusivas[3]->id, $exclusivas[2]->id, $normal->id],
            $this->ids($this->nearby($member, ['limit' => 3])->assertOk()),
        );

        // Límite 5: los demás comercios no alcanzan a llenarlo, se completa con más
        // exclusivas (que siguen encabezando la lista).
        $this->assertSame(
            [$exclusivas[3]->id, $exclusivas[2]->id, $exclusivas[1]->id, $exclusivas[0]->id, $normal->id],
            $this->ids($this->nearby($member, ['limit' => 5])->assertOk()),
        );

        // Su alianza es la única con recompensas: salen todas las que pida el límite.
        $normal->delete();
        $this->assertCount(4, $this->ids($this->nearby($member, ['limit' => 5])->assertOk()));
    }

    public function test_limit_por_defecto_5_y_topado_a_50(): void
    {
        $shops = collect(range(1, 30))->map(fn () => $this->shop(0.009));
        $shops->each(fn ($shop) => $this->reward($shop, 10) && $this->reward($shop, 20));
        $user = $this->user(100);

        $this->assertCount(5, $this->ids($this->nearby($user)->assertOk()));
        $this->assertCount(50, $this->ids($this->nearby($user, ['limit' => 1000])->assertOk()));
    }
}
