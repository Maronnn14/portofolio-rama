<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('correct-password'),
        ]);
    }

    #[Test]
    public function user_can_login_with_valid_credentials()
    {
        $this->createUser();

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'correct-password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token',
                'user' => ['name', 'email'],
            ]);
    }

    #[Test]
    public function user_cannot_login_with_invalid_credentials()
    {
        $this->createUser();

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function user_cannot_login_with_nonexistent_email()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function login_requires_email_and_password()
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    #[Test]
    public function authenticated_user_can_check_status()
    {
        $user = $this->createUser();
        $token = $user->createToken('admin-token', ['admin:write'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/auth/check');

        $response->assertOk()
            ->assertJson([
                'authenticated' => true,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }

    #[Test]
    public function guest_cannot_check_status()
    {
        $response = $this->getJson('/api/auth/check');

        $response->assertUnauthorized();
    }

    #[Test]
    public function authenticated_user_can_logout()
    {
        $user = $this->createUser();
        $token = $user->createToken('admin-token', ['admin:write'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/auth/logout');

        $response->assertOk()
            ->assertJson(['message' => 'Logged out successfully']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function guest_cannot_logout()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertUnauthorized();
    }

    #[Test]
    public function token_is_invalidated_after_logout()
    {
        $user = $this->createUser();
        $token = $user->createToken('admin-token', ['admin:write'])->plainTextToken;

        $this->withToken($token)->postJson('/api/auth/logout');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function user_can_change_password_with_valid_current_password()
    {
        $user = $this->createUser();
        $token = $user->createToken('admin-token', ['admin:write'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/auth/change-password', [
                'current_password' => 'correct-password',
                'new_password' => 'new-secure-password',
            ]);

        $response->assertOk()
            ->assertJsonStructure(['message', 'token']);
    }

    #[Test]
    public function user_cannot_change_password_with_wrong_current_password()
    {
        $user = $this->createUser();
        $token = $user->createToken('admin-token', ['admin:write'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/auth/change-password', [
                'current_password' => 'wrong-password',
                'new_password' => 'new-secure-password',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    #[Test]
    public function password_change_revokes_old_tokens()
    {
        $user = $this->createUser();
        $token = $user->createToken('admin-token', ['admin:write'])->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/auth/change-password', [
                'current_password' => 'correct-password',
                'new_password' => 'new-secure-password',
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    #[Test]
    public function new_password_must_differ_from_current()
    {
        $user = $this->createUser();
        $token = $user->createToken('admin-token', ['admin:write'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/auth/change-password', [
                'current_password' => 'correct-password',
                'new_password' => 'correct-password',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['new_password']);
    }

    #[Test]
    public function new_password_must_be_at_least_eight_characters()
    {
        $user = $this->createUser();
        $token = $user->createToken('admin-token', ['admin:write'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/auth/change-password', [
                'current_password' => 'correct-password',
                'new_password' => 'short',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['new_password']);
    }

    #[Test]
    public function login_token_has_admin_write_ability()
    {
        $this->createUser();

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'correct-password',
        ]);

        $token = $response->json('token');
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        $this->assertTrue($accessToken->can('admin:write'));
    }
}
