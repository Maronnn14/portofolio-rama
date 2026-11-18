<?php

namespace Tests\Feature\Api;

use App\Models\Experience;
use App\Models\GalleryImage;
use App\Models\Interest;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Social;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PortfolioApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_can_list_projects()
    {
        Project::factory()->count(3)->create();

        $response = $this->getJson('/api/projects');

        $response->assertOk();
        $this->assertCount(3, $response->json());
    }

    #[Test]
    public function guests_can_view_single_project()
    {
        $project = Project::factory()->create(['name' => 'Test Project']);

        $response = $this->getJson("/api/projects/{$project->id}");

        $response->assertOk()
            ->assertJson(['name' => 'Test Project']);
    }

    #[Test]
    public function viewing_nonexistent_project_returns_404()
    {
        $response = $this->getJson('/api/projects/9999');

        $response->assertNotFound();
    }

    #[Test]
    public function guests_can_list_skills()
    {
        Skill::factory()->count(4)->create();

        $response = $this->getJson('/api/skills');

        $response->assertOk();
        $this->assertCount(4, $response->json());
    }

    #[Test]
    public function guests_can_list_experiences()
    {
        Experience::factory()->count(2)->create();

        $response = $this->getJson('/api/experiences');

        $response->assertOk();
        $this->assertCount(2, $response->json());
    }

    #[Test]
    public function guests_can_list_socials()
    {
        Social::factory()->create(['visible' => true]);
        Social::factory()->create(['visible' => false]);

        $response = $this->getJson('/api/socials');

        $response->assertOk();
        $this->assertCount(1, $response->json());
    }

    #[Test]
    public function guests_can_list_interests()
    {
        Interest::factory()->count(3)->create();

        $response = $this->getJson('/api/interests');

        $response->assertOk();
        $this->assertCount(3, $response->json());
    }

    #[Test]
    public function guests_can_list_gallery()
    {
        GalleryImage::factory()->create(['visible' => true]);
        GalleryImage::factory()->create(['visible' => false]);

        $response = $this->getJson('/api/gallery');

        $response->assertOk();
        $this->assertCount(1, $response->json());
    }

    #[Test]
    public function settings_are_public()
    {
        $response = $this->getJson('/api/settings');

        $response->assertOk();
    }
}
