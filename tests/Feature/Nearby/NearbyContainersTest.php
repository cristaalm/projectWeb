<?php

namespace Tests\Feature\Nearby;

use App\Enums\ContainerStatus;
use App\Models\Container;
use App\Models\ContainerSensor;
use App\Models\MaterialTypes;
use App\Models\User;
use Illuminate\Support\Str;

class NearbyContainersTest extends NearbyTestCase
{
    private MaterialTypes $plastic;

    private MaterialTypes $aluminum;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plastic = MaterialTypes::create(['name' => 'Plástico', 'slug' => 'plastic', 'points' => 15, 'is_active' => true]);
        $this->aluminum = MaterialTypes::create(['name' => 'Aluminio', 'slug' => 'aluminum', 'points' => 35, 'is_active' => true]);
    }

    private function container(?float $latOffset, array $attributes = []): Container
    {
        return Container::create([
            'name' => 'Contenedor '.Str::random(4),
            'serial_number' => 'SN-'.Str::random(8),
            'location' => 'Jardín principal',
            'latitude' => $latOffset === null ? null : self::LAT + $latOffset,
            'longitude' => $latOffset === null ? null : self::LNG,
            'status' => ContainerStatus::ACTIVE,
            ...$attributes,
        ]);
    }

    private function sensor(Container $container, MaterialTypes $material, float $level, $measuredAt = null): void
    {
        ContainerSensor::create([
            'container_id' => $container->id,
            'sensor_key' => $material->slug,
            'material_type_id' => $material->id,
            'fill_level' => $level,
            'updated_at' => $measuredAt ?? now(),
        ]);
    }

    private function nearby(User $user, array $body)
    {
        return $this->actingAs($user)->postJson('/api/nearby/containers', $body);
    }

    private function ids($response): array
    {
        return array_column($response->json('data.containers'), 'id');
    }

    public function test_contenedores_activos_del_radio_por_distancia(): void
    {
        $lejos = $this->container(0.027);  // ~3 km
        $cerca = $this->container(0.009);  // ~1 km
        $this->container(0.09);            // ~10 km
        $this->container(null);
        $this->container(0.009, ['status' => ContainerStatus::INACTIVE]);

        $response = $this->nearby($this->user(), $this->location())->assertOk();

        $this->assertSame([$cerca->id, $lejos->id], $this->ids($response));
        $this->assertEqualsWithDelta(1000, $response->json('data.containers.0.distance_m'), 10);
        // Sin lecturas todavía: sale, con la lista de compartimentos vacía.
        $this->assertSame([], $response->json('data.containers.0.sensors'));
        $this->assertArrayNotHasKey('serial_number', $response->json('data.containers.0'));
        $this->assertArrayNotHasKey('api_token', $response->json('data.containers.0'));
    }

    public function test_llenado_por_material_y_exclusion_si_todos_estan_llenos(): void
    {
        $parcial = $this->container(0.009);
        $this->sensor($parcial, $this->plastic, 95);
        $this->sensor($parcial, $this->aluminum, 40.5);

        $lleno = $this->container(0.009);
        $this->sensor($lleno, $this->plastic, 90);
        $this->sensor($lleno, $this->aluminum, 100);

        $response = $this->nearby($this->user(), $this->location())->assertOk();

        $this->assertSame([$parcial->id], $this->ids($response));
        $sensors = collect($response->json('data.containers.0.sensors'))->keyBy('sensor_key');
        $this->assertSame(['slug' => 'plastic', 'name' => 'Plástico'], $sensors['plastic']['material']);
        $this->assertEquals(95, $sensors['plastic']['fill_level']);
        $this->assertTrue($sensors['plastic']['is_full']);
        $this->assertEquals(40.5, $sensors['aluminum']['fill_level']);
        $this->assertFalse($sensors['aluminum']['is_full']);
    }

    public function test_dato_viejo_se_marca_sin_excluir(): void
    {
        $container = $this->container(0.009);
        $this->sensor($container, $this->plastic, 20, now()->subHours(7));
        $this->sensor($container, $this->aluminum, 30, now()->subHours(5));

        $response = $this->nearby($this->user(), $this->location())->assertOk();

        $sensors = collect($response->json('data.containers.0.sensors'))->keyBy('sensor_key');
        $this->assertTrue($sensors['plastic']['is_stale']);
        $this->assertFalse($sensors['aluminum']['is_stale']);
        $this->assertNotNull($sensors['plastic']['measured_at']);
    }

    public function test_sin_ubicacion_o_ubicacion_invalida_es_422(): void
    {
        $user = $this->user();

        $this->nearby($user, [])->assertStatus(422)->assertJsonValidationErrors(['latitude', 'longitude', 'accuracy', 'captured_at']);
        $this->nearby($user, $this->location(['accuracy' => 800]))->assertStatus(422)->assertJsonValidationErrors('accuracy');
        $this->nearby($user, $this->location(['captured_at' => now()->subMinutes(30)->toIso8601String()]))->assertStatus(422)->assertJsonValidationErrors('captured_at');
    }

    public function test_limit_por_defecto_5_y_topado_a_50(): void
    {
        collect(range(1, 55))->each(fn () => $this->container(0.009));
        $user = $this->user();

        $this->assertCount(5, $this->ids($this->nearby($user, $this->location())->assertOk()));
        $this->assertCount(50, $this->ids($this->nearby($user, $this->location(['limit' => 1000]))->assertOk()));
    }
}
