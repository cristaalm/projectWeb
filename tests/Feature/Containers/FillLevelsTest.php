<?php

namespace Tests\Feature\Containers;

use App\Enums\ContainerStatus;
use App\Enums\FillReadingSource;
use App\Models\Container;
use App\Models\MaterialTypes;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class FillLevelsTest extends TestCase
{
    use RefreshDatabase;

    private Container $container;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['plastic' => 15, 'aluminum' => 35, 'other' => 0] as $slug => $points) {
            MaterialTypes::create(['name' => ucfirst($slug), 'slug' => $slug, 'points' => $points, 'is_active' => true]);
        }

        $this->container = new Container([
            'name' => 'Contenedor de prueba',
            'serial_number' => 'SN-'.Str::random(8),
            'location' => 'Prueba',
            'status' => ContainerStatus::ACTIVE,
        ]);
        $this->token = $this->container->assignNewToken();
        $this->container->save();
    }

    private function report(array $body)
    {
        return $this->postJson('/api/containers/fill-levels', $body, ['X-Container-Token' => $this->token]);
    }

    public function test_reporte_guarda_estado_actual_e_historial(): void
    {
        $this->report(['sensors' => [
            ['material' => 'plastic', 'fill_level' => 72.5],
            ['material' => 'aluminum', 'fill_level' => 18, 'sensor_key' => 'latas'],
        ]])
            ->assertOk()
            ->assertJsonPath('data.sensors.0.sensor_key', 'plastic')
            ->assertJsonPath('data.sensors.0.applied', true)
            ->assertJsonPath('data.sensors.1.sensor_key', 'latas');

        $this->assertSame(['latas', 'plastic'], $this->container->sensors()->orderBy('sensor_key')->pluck('sensor_key')->all());
        $this->assertSame(2, $this->container->fillReadings()->where('source', FillReadingSource::REPORT->value)->count());
    }

    public function test_lectura_mas_vieja_queda_solo_en_historial(): void
    {
        $this->report(['sensors' => [['material' => 'plastic', 'fill_level' => 60]]])->assertOk();

        $this->report([
            'measured_at' => now()->subHour()->toIso8601String(),
            'sensors' => [['material' => 'plastic', 'fill_level' => 10]],
        ])->assertOk()->assertJsonPath('data.sensors.0.applied', false);

        $this->assertEquals(60, $this->container->sensors()->value('fill_level'));
        $this->assertSame(2, $this->container->fillReadings()->count());
    }

    public function test_rechaza_compartimento_repetido_y_hora_futura(): void
    {
        $this->report(['sensors' => [
            ['material' => 'plastic', 'fill_level' => 10],
            ['material' => 'aluminum', 'fill_level' => 10, 'sensor_key' => 'plastic'],
        ]])->assertStatus(422)->assertJsonValidationErrors('sensors.1.sensor_key');

        $this->report([
            'measured_at' => now()->addHour()->toIso8601String(),
            'sensors' => [['material' => 'plastic', 'fill_level' => 10]],
        ])->assertStatus(422)->assertJsonValidationErrors('measured_at');

        $this->assertSame(0, $this->container->fillReadings()->count());
    }

    public function test_contenedor_inactivo_no_puede_reportar(): void
    {
        $this->container->update(['status' => ContainerStatus::INACTIVE]);

        $this->report(['sensors' => [['material' => 'plastic', 'fill_level' => 10]]])->assertStatus(403);
    }

    public function test_escaneo_registra_llenado_una_sola_vez(): void
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

        $body = [
            'event_id' => (string) Str::uuid(),
            'code_identity' => $user->code_identity,
            'material' => 'other',
            'fill_levels' => [['material' => 'plastic', 'fill_level' => 80]],
        ];
        $headers = ['X-Container-Token' => $this->token];

        $this->postJson('/api/scans', $body, $headers)->assertCreated();
        $this->postJson('/api/scans', $body, $headers)->assertOk()->assertJsonPath('data.duplicate', true);

        $reading = $this->container->fillReadings()->sole();
        $this->assertSame(FillReadingSource::SCAN, $reading->source);
        $this->assertNotNull($reading->scan_id);
        $this->assertEquals(80, $this->container->sensors()->value('fill_level'));
    }

    public function test_purga_solo_borra_lo_mas_viejo_que_la_retencion(): void
    {
        config(['containers.fill_readings_retention_months' => 12]);

        $row = [
            'container_id' => $this->container->id,
            'sensor_key' => 'plastic',
            'fill_level' => 50,
            'measured_at' => now(),
            'source' => FillReadingSource::REPORT->value,
        ];
        DB::table('container_fill_readings')->insert([
            $row + ['created_at' => now()->subMonths(13)],
            $row + ['created_at' => now()->subMonths(11)],
        ]);

        $this->artisan('containers:prune-fill-readings')->assertSuccessful();

        $this->assertSame(1, $this->container->fillReadings()->count());
    }
}
