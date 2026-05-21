<?php

namespace Tests\Unit\Models;

use App\Models\Social;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SocialTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_social_link()
    {
        $social = Social::create([
            'platform' => 'GitHub',
            'url' => 'https://github.com/test',
            'label' => '@test',
            'icon' => 'github',
            'visible' => true,
            'sort_order' => 0,
        ]);

        $this->assertDatabaseHas('socials', ['platform' => 'GitHub']);
        $this->assertEquals('https://github.com/test', $social->url);
    }

    #[Test]
    public function it_casts_visible_to_boolean()
    {
        $social = Social::create([
            'platform' => 'Twitter',
            'url' => 'https://twitter.com/test',
            'visible' => 1,
        ]);

        $this->assertIsBool($social->visible);
        $this->assertTrue($social->visible);
    }

    #[Test]
    public function it_casts_sort_order_to_integer()
    {
        $social = Social::create([
            'platform' => 'LinkedIn',
            'url' => 'https://linkedin.com/in/test',
            'sort_order' => '3',
        ]);

        $this->assertIsInt($social->sort_order);
        $this->assertEquals(3, $social->sort_order);
    }

    #[Test]
    public function it_visible_defaults_to_true_from_migration()
    {
        $social = Social::create([
            'platform' => 'Instagram',
            'url' => 'https://instagram.com/test',
        ]);

        $this->assertTrue($social->fresh()->visible);
    }
}
