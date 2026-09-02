<?php

namespace Tests\Feature\Profile;

use App\Models\Role;
use App\Models\User;
use App\Models\UserSocialAccount;
use App\Services\Auth\GoogleIdTokenVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class SocialAccountLinkingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Reemplaza el verificador real de Google por uno que devuelve los claims
     * dados, igual que en SocialLoginTest — evita depender de la red/JWKS real.
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
     * UserFactory referencia columnas de antes del rediseño del esquema y
     * falla contra la tabla `users` actual (ver SocialLoginTest) — se arma el
     * usuario a mano igual que ahí.
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
            'has_usable_password' => true,
            'code_identity' => $digits12 . $checkDigit,
            'role_id' => Role::where('name', 'member')->firstOrFail()->id,
        ], $overrides));
    }

    public function test_links_a_new_google_account_to_the_authenticated_user(): void
    {
        $user = $this->createUser();

        $this->fakeGoogleVerifier([
            'sub' => 'google-link-1',
            'email' => 'otro-correo@gmail.com',
            'given_name' => 'Test',
            'family_name' => 'User',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/profile/social', [
            'provider' => 'google',
            'id_token' => 'fake',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.user.social_providers', ['google']);
        $this->assertDatabaseHas('user_social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-link-1',
        ]);
    }

    public function test_rejects_linking_a_google_account_already_linked_to_another_user(): void
    {
        $owner = $this->createUser();
        UserSocialAccount::create(['user_id' => $owner->id, 'provider' => 'google', 'provider_id' => 'google-taken']);

        $other = $this->createUser();

        $this->fakeGoogleVerifier([
            'sub' => 'google-taken',
            'email' => 'no-importa@gmail.com',
            'given_name' => 'Test',
            'family_name' => 'User',
        ]);

        Sanctum::actingAs($other);

        $response = $this->postJson('/api/profile/social', [
            'provider' => 'google',
            'id_token' => 'fake',
        ]);

        $response->assertStatus(422);
        $this->assertSame(1, UserSocialAccount::where('provider_id', 'google-taken')->count());
    }

    public function test_unlinks_with_correct_password_when_another_access_method_remains(): void
    {
        $user = $this->createUser();
        UserSocialAccount::create(['user_id' => $user->id, 'provider' => 'google', 'provider_id' => 'google-unlink-1']);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/profile/social/unlink', [
            'provider' => 'google',
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.user.social_providers', []);
        $this->assertDatabaseMissing('user_social_accounts', ['user_id' => $user->id]);
    }

    public function test_unlink_requires_two_factor_code_when_enabled(): void
    {
        $secret = (new Google2FA())->generateSecretKey();
        $user = $this->createUser(['two_factor_status' => true, 'google2fa_secret' => $secret]);
        UserSocialAccount::create(['user_id' => $user->id, 'provider' => 'google', 'provider_id' => 'google-unlink-2fa']);

        $validCode = (new Google2FA())->getCurrentOtp($secret);

        Sanctum::actingAs($user);

        $rejected = $this->postJson('/api/profile/social/unlink', [
            'provider' => 'google',
            'password' => 'password',
        ]);
        $rejected->assertStatus(422);

        Sanctum::actingAs($user);

        $accepted = $this->postJson('/api/profile/social/unlink', [
            'provider' => 'google',
            'password' => 'password',
            'token2FA' => $validCode,
        ]);
        $accepted->assertOk();
    }

    public function test_rejects_unlinking_the_only_access_method(): void
    {
        $user = $this->createUser(['has_usable_password' => false]);
        UserSocialAccount::create(['user_id' => $user->id, 'provider' => 'google', 'provider_id' => 'google-only']);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/profile/social/unlink', [
            'provider' => 'google',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseHas('user_social_accounts', ['user_id' => $user->id, 'provider' => 'google']);
    }

    public function test_social_only_user_can_set_a_password_without_current_password(): void
    {
        $user = $this->createUser(['has_usable_password' => false]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/profile/password', [
            'password' => 'NuevaPass123!',
            'password_confirmation' => 'NuevaPass123!',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.user.has_usable_password', true);

        Sanctum::actingAs($user->fresh());

        $again = $this->postJson('/api/profile/password', [
            'password' => 'OtraPass123!',
            'password_confirmation' => 'OtraPass123!',
        ]);

        $again->assertStatus(422);
    }
}
