<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_user()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertDatabaseHas('users', ['email' => 'admin@test.com']);
        $this->assertEquals('Admin', $user->name);
    }

    #[Test]
    public function it_hashes_password()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'plain-password',
        ]);

        $this->assertNotEquals('plain-password', $user->password);
        $this->assertTrue(password_verify('plain-password', $user->password));
    }

    #[Test]
    public function it_casts_email_verified_at_to_datetime()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $user->email_verified_at);
    }

    #[Test]
    public function it_can_create_admin_token()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $token = $user->createToken('admin-token', ['admin:write']);

        $this->assertNotEmpty($token->plainTextToken);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'admin-token',
        ]);
    }

    #[Test]
    public function it_uses_has_api_tokens_trait()
    {
        $user = new User();
        $traits = class_uses_recursive(User::class);
        $this->assertContains(\Laravel\Sanctum\HasApiTokens::class, $traits);
    }
}
