<?php

namespace Tests\Unit\Models;

use App\Models\GalleryImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GalleryImageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_gallery_image()
    {
        $image = GalleryImage::create([
            'url' => 'https://example.com/image.jpg',
            'alt' => 'A beautiful sunset',
            'category' => 'Nature',
            'visible' => true,
            'sort_order' => 0,
        ]);

        $this->assertDatabaseHas('gallery_images', ['url' => 'https://example.com/image.jpg']);
        $this->assertEquals('A beautiful sunset', $image->alt);
    }

    #[Test]
    public function it_casts_visible_to_boolean()
    {
        $image = GalleryImage::create([
            'url' => 'https://example.com/img.jpg',
            'visible' => 1,
        ]);

        $this->assertIsBool($image->visible);
        $this->assertTrue($image->visible);
    }

    #[Test]
    public function it_casts_sort_order_to_integer()
    {
        $image = GalleryImage::create([
            'url' => 'https://example.com/img2.jpg',
            'sort_order' => '5',
        ]);

        $this->assertIsInt($image->sort_order);
        $this->assertEquals(5, $image->sort_order);
    }

    #[Test]
    public function it_allows_null_alt_and_category()
    {
        $image = GalleryImage::create([
            'url' => 'https://example.com/img3.jpg',
        ]);

        $this->assertNull($image->alt);
        $this->assertNull($image->category);
    }

    #[Test]
    public function it_visible_defaults_to_true_from_migration()
    {
        $image = GalleryImage::create([
            'url' => 'https://example.com/img4.jpg',
        ]);

        $this->assertTrue($image->fresh()->visible);
    }
}
