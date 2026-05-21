<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_project()
    {
        $project = Project::create([
            'name' => 'Test Project',
            'short_desc' => 'A short description',
            'full_desc' => 'A full description',
            'category' => 'Full Stack',
            'status' => 'published',
            'thumbnail' => 'https://example.com/img.jpg',
            'tech' => ['Laravel', 'Vue.js'],
            'live_url' => 'https://example.com',
            'source_url' => 'https://github.com/test',
            'featured' => true,
            'gallery' => ['https://example.com/img1.jpg'],
            'sort_order' => 1,
        ]);

        $this->assertDatabaseHas('projects', ['name' => 'Test Project']);
        $this->assertEquals('Test Project', $project->name);
        $this->assertEquals(['Laravel', 'Vue.js'], $project->tech);
    }

    #[Test]
    public function it_casts_tech_and_gallery_to_array()
    {
        $project = Project::create([
            'name' => 'Array Cast Test',
            'short_desc' => 'Testing casts',
            'tech' => ['PHP', 'MySQL'],
            'gallery' => ['img1.jpg', 'img2.jpg'],
        ]);

        $this->assertIsArray($project->tech);
        $this->assertIsArray($project->gallery);
        $this->assertEquals(['PHP', 'MySQL'], $project->tech);
        $this->assertEquals(['img1.jpg', 'img2.jpg'], $project->gallery);
    }

    #[Test]
    public function it_casts_featured_to_boolean()
    {
        $project = Project::create([
            'name' => 'Boolean Cast Test',
            'short_desc' => 'Testing featured cast',
            'featured' => 1,
        ]);

        $this->assertIsBool($project->featured);
        $this->assertTrue($project->featured);
    }

    #[Test]
    public function it_casts_sort_order_to_integer()
    {
        $project = Project::create([
            'name' => 'Integer Cast Test',
            'short_desc' => 'Testing sort_order cast',
            'sort_order' => '5',
        ]);

        $this->assertIsInt($project->sort_order);
        $this->assertEquals(5, $project->sort_order);
    }

    #[Test]
    public function it_has_fillable_fields()
    {
        $project = Project::create([
            'name' => 'Fillable Test',
            'short_desc' => 'Testing fillable',
        ]);

        $this->assertEquals('Fillable Test', $project->name);
        $this->assertEquals('Testing fillable', $project->short_desc);
    }
}
