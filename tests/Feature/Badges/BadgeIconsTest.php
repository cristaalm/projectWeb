<?php

namespace Tests\Feature\Badges;

use App\Models\Badge;
use App\Models\Role;
use App\Models\User;
use App\Support\BadgeIcons;
use App\Support\BadgeTiers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BadgeIconsTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $digits12 = str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);

        return User::create([
            'name' => 'Test',
            'last_name' => 'User',
            'email' => 'test-'.uniqid().'@example.com',
            'phone' => '5550000000',
            'password' => 'password',
            'code_identity' => $digits12.User::calculateEan13CheckDigit($digits12),
            'role_id' => Role::where('name', $role)->firstOrFail()->id,
        ]);
    }

    private function badge(array $attributes = []): Badge
    {
        return Badge::create(array_merge([
            'name' => 'Insignia '.uniqid(),
            'icon' => 'bx-recycle',
            'recycles_required' => 10,
            'points_awarded' => 20,
            'status' => true,
        ], $attributes));
    }

    public function test_cada_icono_del_catalogo_tiene_su_svg_generado(): void
    {
        foreach (BadgeIcons::names() as $name) {
            $path = public_path(BadgeIcons::PUBLIC_DIR."/{$name}.svg");

            $this->assertFileExists($path, "Falta el SVG de '{$name}': corre php artisan icons:build");
            $this->assertStringStartsWith('<svg', file_get_contents($path));
        }

        $this->assertArrayHasKey(BadgeIcons::DEFAULT, BadgeIcons::ICONS);
    }

    public function test_el_nivel_depende_de_los_reciclajes_requeridos(): void
    {
        $this->assertSame('brote', BadgeTiers::forRecycles(1)['key']);
        $this->assertSame('brote', BadgeTiers::forRecycles(9)['key']);
        $this->assertSame('musgo', BadgeTiers::forRecycles(10)['key']);
        $this->assertSame('musgo', BadgeTiers::forRecycles(24)['key']);
        $this->assertSame('bosque', BadgeTiers::forRecycles(25)['key']);
        $this->assertSame('follaje', BadgeTiers::forRecycles(50)['key']);
        $this->assertSame('follaje', BadgeTiers::forRecycles(500)['key']);
    }

    public function test_lista_iconos_icono_predeterminado_y_niveles(): void
    {
        $response = $this->actingAs($this->userWithRole('member'))
            ->getJson('/api/badges/icons')
            ->assertOk()
            ->assertJsonCount(count(BadgeIcons::ICONS), 'data.icons')
            ->assertJsonPath('data.icons.0.name', 'bx-recycle')
            ->assertJsonPath('data.icons.0.label', 'Reciclaje')
            ->assertJsonPath('data.default_icon', 'bx-medal')
            ->assertJsonCount(count(BadgeTiers::TIERS), 'data.tiers')
            ->assertJsonPath('data.tiers.0.key', 'brote');

        $this->assertStringEndsWith('/icons/badges/bx-recycle.svg', $response->json('data.icons.0.url'));
    }

    public function test_el_catalogo_para_la_app_movil_trae_icon_url_y_nivel(): void
    {
        $this->badge(['icon' => 'bx-trophy', 'recycles_required' => 30]);
        $this->badge(['icon' => null, 'recycles_required' => 5]);

        $response = $this->actingAs($this->userWithRole('member'))
            ->getJson('/api/badges/catalog')
            ->assertOk()
            ->assertJsonPath('data.badges.0.icon', null)
            ->assertJsonPath('data.badges.0.tier.key', 'brote')
            ->assertJsonPath('data.badges.0.tier.background', '#7CB88F')
            ->assertJsonPath('data.badges.1.tier.key', 'bosque')
            ->assertJsonPath('data.badges.1.tier.color', '#FFFFFF');

        // Sin icono asignado cae al predeterminado, para que la app siempre tenga qué pintar.
        $this->assertStringEndsWith('/icons/badges/bx-medal.svg', $response->json('data.badges.0.icon_url'));
        $this->assertStringEndsWith('/icons/badges/bx-trophy.svg', $response->json('data.badges.1.icon_url'));
    }

    public function test_el_progreso_del_mes_incluye_el_emblema(): void
    {
        $this->badge(['icon' => 'bx-leaf', 'recycles_required' => 60]);

        $response = $this->actingAs($this->userWithRole('member'))
            ->getJson('/api/badges/my-progress')
            ->assertOk()
            ->assertJsonPath('data.progress.0.badge.tier.key', 'follaje');

        $this->assertStringEndsWith('/icons/badges/bx-leaf.svg', $response->json('data.progress.0.badge.icon_url'));
    }

    public function test_rechaza_iconos_fuera_del_catalogo(): void
    {
        $this->actingAs($this->userWithRole('superadmin'))
            ->postJson('/api/badges', [
                'name' => 'Nueva', 'icon' => 'bx-no-existe', 'recycles_required' => 5, 'points_awarded' => 10, 'status' => true,
            ])
            ->assertStatus(422);

        $this->actingAs($this->userWithRole('superadmin'))
            ->postJson('/api/badges', [
                'name' => 'Nueva', 'icon' => 'bx-sun', 'recycles_required' => 5, 'points_awarded' => 10, 'status' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('data.badge.icon', 'bx-sun')
            ->assertJsonPath('data.badge.tier.key', 'brote');
    }
}
