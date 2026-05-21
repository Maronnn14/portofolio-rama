<?php

namespace Tests\Feature\Api;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SiteSettingApiTest extends TestCase
{
    use RefreshDatabase;

    private function adminToken(): string
    {
        $user = User::factory()->create();
        return $user->createToken('admin-token', ['admin:write'])->plainTextToken;
    }

    private function tokenWithoutAbility(): string
    {
        $user = User::factory()->create();
        return $user->createToken('no-ability-token', [])->plainTextToken;
    }

    // ====== HAPPY PATH — READ ======

    #[Test]
    public function guests_can_read_all_settings()
    {
        SiteSetting::create(['key' => 'site_name', 'value' => 'My Portfolio']);
        SiteSetting::create(['key' => 'maxLength', 'value' => '500']);

        $response = $this->getJson('/api/settings');

        $response->assertOk()
            ->assertJson([
                'site_name' => 'My Portfolio',
                'maxLength' => 500,
            ]);
    }

    #[Test]
    public function it_casts_boolean_settings_properly()
    {
        SiteSetting::create(['key' => 'allowPosts', 'value' => '1']);
        SiteSetting::create(['key' => 'moderationMode', 'value' => '0']);

        $response = $this->getJson('/api/settings');

        $response->assertOk();
        $this->assertTrue($response->json('allowPosts'));
        $this->assertFalse($response->json('moderationMode'));
    }

    #[Test]
    public function it_casts_integer_settings_properly()
    {
        SiteSetting::create(['key' => 'maxLength', 'value' => '300']);

        $response = $this->getJson('/api/settings');

        $response->assertOk();
        $this->assertSame(300, $response->json('maxLength'));
        $this->assertIsInt($response->json('maxLength'));
    }

    #[Test]
    public function it_returns_empty_when_no_settings_exist()
    {
        $response = $this->getJson('/api/settings');

        $response->assertOk();
        $this->assertEmpty($response->json());
    }

    #[Test]
    public function it_keeps_string_settings_as_strings()
    {
        SiteSetting::create(['key' => 'site_name', 'value' => 'My Portfolio']);

        $response = $this->getJson('/api/settings');

        $response->assertOk();
        $this->assertIsString($response->json('site_name'));
    }

    // ====== HAPPY PATH — UPDATE ======

    #[Test]
    public function admin_can_update_string_settings()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'site_name' => 'Updated Portfolio',
                'site_description' => 'A great site',
            ]);

        $response->assertOk();
        $this->assertEquals('Updated Portfolio', $response->json('site_name'));

        $this->assertDatabaseHas('site_settings', [
            'key' => 'site_name',
            'value' => json_encode('Updated Portfolio'),
        ]);
    }

    #[Test]
    public function admin_can_update_boolean_settings()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'allowPosts' => true,
                'moderationMode' => false,
            ]);

        $response->assertOk();
        // update() returns raw DB values without boolean casting
        $this->assertSame('1', $response->json('allowPosts'));
        $this->assertSame('0', $response->json('moderationMode'));
    }

    #[Test]
    public function admin_can_update_integer_settings()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'maxLength' => 1000,
            ]);

        $response->assertOk();
        // update() returns raw DB values without integer casting
        $this->assertSame('1000', $response->json('maxLength'));
    }

    #[Test]
    public function admin_can_partially_update_single_setting()
    {
        SiteSetting::create(['key' => 'site_name', 'value' => 'Old Name']);
        SiteSetting::create(['key' => 'footer_text', 'value' => 'Old Footer']);

        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'site_name' => 'New Name',
            ]);

        $response->assertOk();
        $this->assertEquals('New Name', $response->json('site_name'));
        $this->assertEquals('Old Footer', $response->json('footer_text'));
    }

    // ====== FAILURE CASES — AUTH ======

    #[Test]
    public function guest_cannot_update_settings()
    {
        $response = $this->putJson('/api/settings', ['site_name' => 'Hacked']);

        $response->assertUnauthorized();
    }

    #[Test]
    public function token_without_admin_ability_cannot_update()
    {
        $token = $this->tokenWithoutAbility();

        $response = $this->withToken($token)
            ->putJson('/api/settings', ['site_name' => 'Hacked']);

        $response->assertForbidden();
    }

    // ====== FAILURE CASES — VALIDATION ======

    #[Test]
    public function it_rejects_non_boolean_value_for_boolean_key()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'allowPosts' => 'not-a-boolean',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['allowPosts']);
    }

    #[Test]
    public function it_rejects_non_integer_value_for_integer_key()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'maxLength' => 'not-an-integer',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['maxLength']);
    }

    #[Test]
    public function it_rejects_string_exceeding_max_length()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'site_name' => str_repeat('a', 501),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['site_name']);
    }

    // ====== EDGE CASES ======

    #[Test]
    public function it_silently_ignores_unknown_keys()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'unknown_key' => 'should not be stored',
            ]);

        $response->assertOk();
        $this->assertDatabaseMissing('site_settings', ['key' => 'unknown_key']);
    }

    #[Test]
    public function it_accepts_boolean_strings_0_and_1()
    {
        // Laravel's boolean validation accepts '0', '1', 'true', 'false', 0, 1
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'allowPosts' => '1',
                'moderationMode' => '0',
            ]);

        $response->assertOk();
        // update() returns raw DB values without boolean casting
        $this->assertSame('1', $response->json('allowPosts'));
        $this->assertSame('0', $response->json('moderationMode'));
    }

    #[Test]
    public function it_accepts_integer_as_string_for_integer_key()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'maxLength' => '500',
            ]);

        $response->assertOk();
        $this->assertSame('500', $response->json('maxLength'));
    }

    #[Test]
    public function it_stores_boolean_as_string_in_database()
    {
        $token = $this->adminToken();

        $this->withToken($token)
            ->putJson('/api/settings', ['allowPosts' => true]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'allowPosts',
            'value' => json_encode('1'),
        ]);
    }

    #[Test]
    public function it_overwrites_existing_setting_on_update()
    {
        SiteSetting::create(['key' => 'site_name', 'value' => json_encode('Original')]);

        $token = $this->adminToken();

        $this->withToken($token)
            ->putJson('/api/settings', ['site_name' => 'Overwritten']);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'site_name',
            'value' => json_encode('Overwritten'),
        ]);

        $this->assertDatabaseCount('site_settings', 1);
    }

    #[Test]
    public function it_accepts_null_value_for_nullable_fields()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'google_analytics_id' => null,
                'footer_text' => null,
            ]);

        $response->assertOk();
        $this->assertSame('', $response->json('google_analytics_id'));
        $this->assertSame('', $response->json('footer_text'));
    }

    #[Test]
    public function it_handles_empty_update_request()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', []);

        $response->assertOk();
        $this->assertEmpty($response->json());
    }

    #[Test]
    public function it_updates_all_allowed_keys_at_once()
    {
        $token = $this->adminToken();

        $payload = [
            'site_name' => 'Full Portfolio',
            'site_description' => 'Complete desc',
            'meta_keywords' => 'laravel, portfolio',
            'meta_author' => 'Rama',
            'google_analytics_id' => 'G-12345',
            'footer_text' => 'Footer',
            'copyright_text' => '2026 Rama',
            'siteTitle' => 'Full Title',
            'metaDesc' => 'Full Meta',
            'allowPosts' => true,
            'moderationMode' => false,
            'maxLength' => 600,
        ];

        $response = $this->withToken($token)
            ->putJson('/api/settings', $payload);

        $response->assertOk();
        // update() returns raw DB values — strings, not typed
        $this->assertSame('Full Portfolio', $response->json('site_name'));
        $this->assertSame('Complete desc', $response->json('site_description'));
        $this->assertSame('laravel, portfolio', $response->json('meta_keywords'));
        $this->assertSame('Rama', $response->json('meta_author'));
        $this->assertSame('G-12345', $response->json('google_analytics_id'));
        $this->assertSame('Footer', $response->json('footer_text'));
        $this->assertSame('2026 Rama', $response->json('copyright_text'));
        $this->assertSame('Full Title', $response->json('siteTitle'));
        $this->assertSame('Full Meta', $response->json('metaDesc'));
        $this->assertSame('1', $response->json('allowPosts'));
        $this->assertSame('0', $response->json('moderationMode'));
        $this->assertSame('600', $response->json('maxLength'));
    }
}


/*
 * ====================================================================
 * TESTABILITY ANALYSIS
 * ====================================================================
 *
 * 1. Boolean/integer casting logic duplicated — LOCs: 32-37 and 47-53
 *    Problem: The BOOLEAN_KEYS and INTEGER_KEYS arrays and their casting
 *    logic are repeated in both index() and update(). If a new key is added
 *    to one but not the other, the types will be inconsistent.
 *    Suggestion: Extract a private helper method or a dedicated Settings service:
 *    Before:
 *        if (in_array($key, self::BOOLEAN_KEYS, true)) { $value = (bool) $value; }
 *    After:
 *        private function castValue(string $key, mixed $value): mixed
 *        {
 *            if (in_array($key, self::BOOLEAN_KEYS, true)) return (bool) $value;
 *            if (in_array($key, self::INTEGER_KEYS, true)) return (int) $value;
 *            return $value;
 *        }
 *
 * 2. Validation rules rebuilt on every request — LOCs: 45-54
 *    Problem: The $rules array inside update() rebuilds every time from
 *    scratch. This is inefficient and duplicates the ALLOWED_KEYS definition.
 *    Suggestion: Cache the rules or extract to a FormRequest:
 *    Before:
 *        $rules = [];
 *        foreach (self::ALLOWED_KEYS as $key) { ... }
 *    After:
 *        use App\Http\Requests\UpdateSiteSettingRequest;
 *        public function update(UpdateSiteSettingRequest $request): JsonResponse
 *        { ... }
 *
 * 3. Value serialization logic mixed into controller — LINE 62
 *    Problem: is_bool($value) ? '1'/'0' : (string) ($value ?? '') is ad-hoc
 *    serialization mixed with controller logic. This should be in the model.
 *    Suggestion: Add a mutator on SiteSetting model or a dedicated method:
 *    Before (in controller):
 *        'value' => is_bool($value) ? ($value ? '1' : '0') : (string)($value ?? ''),
 *    After (in SiteSetting model):
 *        public function setValueAttribute($value): void
 *        {
 *            if (is_bool($value)) {
 *                $this->attributes['value'] = $value ? '1' : '0';
 *            } else {
 *                $this->attributes['value'] = (string) ($value ?? '');
 *            }
 *        }
 *
 * ====================================================================
 */
