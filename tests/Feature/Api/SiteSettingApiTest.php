<?php

namespace Tests\Feature\Api;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SiteSettingApiTest extends TestCase
{
    use RefreshDatabase;

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
        SiteSetting::query()->delete();

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
}
