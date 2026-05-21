<?php

namespace Tests\Feature\Api;

use App\Models\PersonalInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonalInfoApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): array
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $token = $user->createToken('admin-token', ['admin:write'])->plainTextToken;

        return [$user, $token];
    }

    #[Test]
    public function guests_can_view_personal_info()
    {
        PersonalInfo::create(['key' => 'name', 'value' => 'Rama']);
        PersonalInfo::create(['key' => 'role', 'value' => 'Developer']);

        $response = $this->getJson('/api/personal-info');

        $response->assertOk()
            ->assertJson([
                'name' => 'Rama',
                'role' => 'Developer',
            ]);
    }

    #[Test]
    public function guests_cannot_update_personal_info()
    {
        $response = $this->putJson('/api/personal-info', [
            'name' => 'Hacker',
        ]);

        $response->assertUnauthorized();
    }

    #[Test]
    public function admin_can_update_personal_info()
    {
        [, $token] = $this->actingAsAdmin();

        $response = $this->withToken($token)
            ->putJson('/api/personal-info', [
                'name' => 'Rama',
                'nickname' => 'Rama',
                'fullName' => 'Rama Adin',
                'role' => 'Full Stack Developer',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('personal_info', ['key' => 'name', 'value' => 'Rama']);
        $this->assertDatabaseHas('personal_info', ['key' => 'role', 'value' => 'Full Stack Developer']);
    }

    #[Test]
    public function admin_can_update_partial_info()
    {
        PersonalInfo::create(['key' => 'name', 'value' => 'Old Name']);
        [, $token] = $this->actingAsAdmin();

        $response = $this->withToken($token)
            ->putJson('/api/personal-info', [
                'name' => 'New Name',
            ]);

        $response->assertOk();
        $this->assertEquals('New Name', PersonalInfo::getAll()['name']);
    }

    #[Test]
    public function it_rejects_invalid_keys()
    {
        [, $token] = $this->actingAsAdmin();

        $response = $this->withToken($token)
            ->putJson('/api/personal-info', [
                'invalid_key' => 'should_not_be_stored',
            ]);

        $response->assertOk();
        $this->assertDatabaseMissing('personal_info', ['key' => 'invalid_key']);
    }

    #[Test]
    public function it_validates_email_format()
    {
        [, $token] = $this->actingAsAdmin();

        $response = $this->withToken($token)
            ->putJson('/api/personal-info', [
                'email' => 'not-an-email',
            ]);

        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_returns_all_info_after_update()
    {
        [, $token] = $this->actingAsAdmin();

        $response = $this->withToken($token)
            ->putJson('/api/personal-info', [
                'name' => 'Rama',
                'role' => 'Developer',
            ]);

        $response->assertOk()
            ->assertJson([
                'name' => 'Rama',
                'role' => 'Developer',
            ]);
    }
}
