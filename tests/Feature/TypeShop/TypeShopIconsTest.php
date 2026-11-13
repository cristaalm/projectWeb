<?php

namespace Tests\Feature\TypeShop;

use App\Models\Role;
use App\Models\TypeShop;
use App\Models\User;
use App\Support\TypeShopIcons;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TypeShopIconsTest extends TestCase
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

    public function test_cada_icono_del_catalogo_tiene_su_svg_generado(): void
    {
        foreach (TypeShopIcons::names() as $name) {
            $path = public_path(TypeShopIcons::PUBLIC_DIR."/{$name}.svg");

            $this->assertFileExists($path, "Falta el SVG de '{$name}': corre php artisan type-shop:build-icons");
            $this->assertStringStartsWith('<svg', file_get_contents($path));
        }

        $this->assertArrayHasKey(TypeShopIcons::DEFAULT, TypeShopIcons::ICONS);
    }

    public function test_lista_el_catalogo_de_iconos_con_su_url(): void
    {
        $response = $this->actingAs($this->userWithRole('member'))
            ->getJson('/api/type-shop/icons')
            ->assertOk()
            ->assertJsonCount(count(TypeShopIcons::ICONS), 'data.icons')
            ->assertJsonPath('data.icons.0.name', 'store')
            ->assertJsonPath('data.icons.0.label', 'Tienda');

        $this->assertStringEndsWith('/icons/type-shop/store.svg', $response->json('data.icons.0.url'));
    }

    public function test_crear_y_editar_categoria_con_icono(): void
    {
        $admin = $this->userWithRole('superadmin');

        $created = $this->actingAs($admin)
            ->postJson('/api/type-shop', ['name' => 'Farmacia', 'icon' => 'pill', 'is_active' => true])
            ->assertCreated()
            ->assertJsonPath('data.type_shop.icon', 'pill');

        $this->assertStringEndsWith('/icons/type-shop/pill.svg', $created->json('data.type_shop.icon_url'));

        $id = $created->json('data.type_shop.id');

        $this->actingAs($admin)
            ->putJson("/api/type-shop/{$id}", ['name' => 'Farmacia', 'icon' => 'hospital-box', 'is_active' => true])
            ->assertOk()
            ->assertJsonPath('data.type_shop.icon', 'hospital-box');

        $this->assertSame('hospital-box', TypeShop::find($id)->icon);
    }

    public function test_rechaza_iconos_fuera_del_catalogo_o_faltantes(): void
    {
        $admin = $this->userWithRole('superadmin');

        $this->actingAs($admin)
            ->postJson('/api/type-shop', ['name' => 'Farmacia', 'icon' => 'no-existe', 'is_active' => true])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->postJson('/api/type-shop', ['name' => 'Farmacia', 'is_active' => true])
            ->assertStatus(422);

        $this->assertDatabaseCount('type_shop', 0);
    }

    public function test_el_listado_de_activas_para_la_app_movil_incluye_el_icono(): void
    {
        TypeShop::create(['name' => 'Cafetería', 'icon' => 'coffee', 'is_active' => true]);
        TypeShop::create(['name' => 'Oculta', 'icon' => 'store', 'is_active' => false]);

        $response = $this->actingAs($this->userWithRole('member'))
            ->getJson('/api/type-shop/active')
            ->assertOk()
            ->assertJsonCount(1, 'data.type_shops')
            ->assertJsonPath('data.type_shops.0.icon', 'coffee');

        $this->assertStringEndsWith('/icons/type-shop/coffee.svg', $response->json('data.type_shops.0.icon_url'));
    }

    public function test_una_categoria_sin_icono_explicito_usa_el_generico(): void
    {
        $typeShop = TypeShop::create(['name' => 'Sin icono', 'is_active' => true])->fresh();

        $this->assertSame(TypeShopIcons::DEFAULT, $typeShop->icon);
    }
}
