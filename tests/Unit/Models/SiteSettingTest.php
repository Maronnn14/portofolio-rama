<?php

namespace Tests\Unit\Models;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SiteSettingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_setting()
    {
        $setting = SiteSetting::create([
            'key' => 'site_name',
            'value' => ['name' => 'My Portfolio'],
        ]);

        $this->assertDatabaseHas('site_settings', ['key' => 'site_name']);
        $this->assertEquals(['name' => 'My Portfolio'], $setting->value);
    }

    #[Test]
    public function it_casts_value_to_array()
    {
        $setting = SiteSetting::create([
            'key' => 'site_name',
            'value' => ['name' => 'My Portfolio'],
        ]);

        $this->assertIsArray($setting->value);
        $this->assertEquals('My Portfolio', $setting->value['name']);
    }

    #[Test]
    public function it_can_retrieve_setting_by_key()
    {
        SiteSetting::create([
            'key' => 'site_name',
            'value' => ['title' => 'Portfolio'],
        ]);

        $result = SiteSetting::getSetting('site_name');

        $this->assertEquals(['title' => 'Portfolio'], $result);
    }

    #[Test]
    public function it_returns_default_when_key_not_found()
    {
        $result = SiteSetting::getSetting('nonexistent', 'default_value');
        $this->assertEquals('default_value', $result);
    }

    #[Test]
    public function it_returns_null_default_when_key_not_found()
    {
        $result = SiteSetting::getSetting('nonexistent');
        $this->assertNull($result);
    }

    #[Test]
    public function it_can_update_setting_value()
    {
        SiteSetting::create([
            'key' => 'site_name',
            'value' => ['old' => 'value'],
        ]);

        SiteSetting::updateOrCreate(
            ['key' => 'site_name'],
            ['value' => ['new' => 'value']]
        );

        $result = SiteSetting::getSetting('site_name');
        $this->assertEquals(['new' => 'value'], $result);
    }
}
