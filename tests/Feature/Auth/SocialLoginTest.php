<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\UserSocialAccount;
use App\Services\Auth\GoogleIdTokenVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Reemplaza el verificador real (que llamaría a la red / JWKS de Google)
     * por uno que simplemente devuelve los claims dados, para poder probar
     * la lógica de negocio de AuthService::resolveSocialUser() sin depender
     * de un idToken real.
     */
    private function fakeGoogleVerifier(array $claims): void
    {
        $fake = new class ($claims) extends GoogleIdTokenVerifier {
            public function __construct(private array $claims)
            {
            }

            public function verify(string $idToken): array
            {
                return $this->claims;
            }
        };

        $this->app->instance(GoogleIdTokenVerifier::class, $fake);
    }

    /**
     * UserFactory (database/factories/UserFactory.php) todavía referencia
     * columnas de antes del rediseño del esquema (curp, total_points,
     * verification_status) y falla contra la tabla `users` actual — se arma
     * el usuario a mano acá en vez de arrastrar ese problema no relacionado
     * a este test.
     */
    private function createUser(array $overrides = []): User
    {
        $digits12 = str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
        $checkDigit = User::calculateEan13CheckDigit($digits12);

        return User::create(array_merge([
            'name' => 'Test',
            'last_name' => 'User',
            'email' => 'test-' . uniqid() . '@example.com',
            'phone' => '5550000000',
            'password' => 'password',
            'code_identity' => $digits12 . $checkDigit,
            'role_id' => Role::where('name', 'member')->firstOrFail()->id,
        ], $overrides));
    }

    public function test_mobile_creates_incomplete_account_when_no_existing_user(): void
    {
        // Las migraciones ya siembran los 5 roles reales del sistema
        // (create_roles_table.php), 'member' entre ellos — no hace falta un factory.
        $this->fakeGoogleVerifier([
            'sub' => 'google-123',
            'email' => 'nuevo@gmail.com',
            'given_name' => 'Nuevo',
            'family_name' => 'Usuario',
        ]);

        $response = $this->postJson('/api/auth/social', [
            'provider' => 'google',
            'id_token' => 'fake',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.user.email', 'nuevo@gmail.com');
        $this->assertNotNull($response->json('data.access_token'));

        $user = User::where('email', 'nuevo@gmail.com')->firstOrFail();
        $this->assertNull($user->phone);
        $this->assertDatabaseHas('user_social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-123',
        ]);
    }

    public function test_links_existing_user_by_email_without_duplicating(): void
    {
        $user = $this->createUser(['email' => 'existente@gmail.com']);

        $this->fakeGoogleVerifier([
            'sub' => 'google-456',
            'email' => 'existente@gmail.com',
            'given_name' => 'Existente',
            'family_name' => 'Usuario',
        ]);

        $response = $this->postJson('/api/auth/social', [
            'provider' => 'google',
            'id_token' => 'fake',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('user_social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-456',
        ]);
        $this->assertSame(1, UserSocialAccount::where('user_id', $user->id)->count());
    }

    public function test_web_session_rejects_unknown_email_without_creating_user(): void
    {
        $this->fakeGoogleVerifier([
            'sub' => 'google-789',
            'email' => 'desconocido@gmail.com',
            'given_name' => 'Desconocido',
            'family_name' => 'Usuario',
        ]);

        // Referer de un dominio "stateful" (SANCTUM_STATEFUL_DOMAINS) para que
        // EnsureFrontendRequestsAreStateful trate la petición como sesión web.
        $response = $this
            ->withHeader('Referer', 'http://localhost')
            ->postJson('/api/auth/social', [
                'provider' => 'google',
                'id_token' => 'fake',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => 'desconocido@gmail.com']);
    }

    public function test_two_factor_active_returns_challenge_instead_of_login(): void
    {
        $this->createUser([
            'email' => 'con2fa@gmail.com',
            'two_factor_status' => true,
        ]);

        $this->fakeGoogleVerifier([
            'sub' => 'google-2fa',
            'email' => 'con2fa@gmail.com',
            'given_name' => 'Con',
            'family_name' => '2FA',
        ]);

        $response = $this->postJson('/api/auth/social', [
            'provider' => 'google',
            'id_token' => 'fake',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.two_factor_required', true);
        $this->assertNull($response->json('data.access_token'));
    }

    public function test_unsupported_provider_is_rejected(): void
    {
        $response = $this->postJson('/api/auth/social', [
            'provider' => 'apple',
            'id_token' => 'fake',
        ]);

        $response->assertStatus(422);
    }
}
