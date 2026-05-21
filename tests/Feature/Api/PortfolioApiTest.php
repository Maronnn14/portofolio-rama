<?php

namespace Tests\Feature\Api;

use App\Models\Experience;
use App\Models\GalleryImage;
use App\Models\Interest;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Social;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PortfolioApiTest extends TestCase
{
    use RefreshDatabase;

    private function adminToken(): string
    {
        $user = User::factory()->create();
        return $user->createToken('admin-token', ['admin:write'])->plainTextToken;
    }

    // ====== PROJECTS ======

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
    public function guests_cannot_create_project()
    {
        $response = $this->postJson('/api/projects', [
            'name' => 'Hacked Project',
            'short_desc' => 'Should not work',
        ]);

        $response->assertUnauthorized();
    }

    #[Test]
    public function admin_can_create_project()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/projects', [
                'name' => 'New Project',
                'short_desc' => 'A new project',
                'full_desc' => 'Full description here',
                'category' => 'Full Stack',
                'tech' => ['Laravel', 'Vue.js'],
                'featured' => true,
            ]);

        $response->assertCreated()
            ->assertJson([
                'name' => 'New Project',
                'short_desc' => 'A new project',
            ]);
    }

    #[Test]
    public function admin_can_update_project()
    {
        $token = $this->adminToken();
        $project = Project::factory()->create(['name' => 'Old Name']);

        $response = $this->withToken($token)
            ->putJson("/api/projects/{$project->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertOk();
        $this->assertEquals('Updated Name', $response->json()['name']);
    }

    #[Test]
    public function admin_can_delete_project()
    {
        $token = $this->adminToken();
        $project = Project::factory()->create();

        $response = $this->withToken($token)
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    #[Test]
    public function create_project_validates_required_fields()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/projects', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'short_desc']);
    }

    #[Test]
    public function viewing_nonexistent_project_returns_404()
    {
        $response = $this->getJson('/api/projects/99999');
        $response->assertNotFound();
    }

    // ====== SKILLS ======

    #[Test]
    public function guests_can_list_skills()
    {
        Skill::create(['name' => 'Skill A', 'sort_order' => 0]);
        Skill::create(['name' => 'Skill B', 'sort_order' => 1]);
        Skill::create(['name' => 'Skill C', 'sort_order' => 2]);

        $response = $this->getJson('/api/skills');

        $response->assertOk();
        $this->assertCount(3, $response->json());
    }

    #[Test]
    public function admin_can_create_skill()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/skills', [
                'name' => 'Laravel',
                'category' => 'Backend',
                'proficiency' => 90,
                'level' => 'Expert',
            ]);

        $response->assertCreated()
            ->assertJson(['name' => 'Laravel', 'proficiency' => 90]);
    }

    #[Test]
    public function admin_can_update_skill()
    {
        $token = $this->adminToken();
        $skill = Skill::factory()->create(['proficiency' => 50]);

        $response = $this->withToken($token)
            ->putJson("/api/skills/{$skill->id}", [
                'proficiency' => 85,
            ]);

        $response->assertOk();
        $this->assertEquals(85, $response->json()['proficiency']);
    }

    #[Test]
    public function skill_proficiency_must_be_between_0_and_100()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/skills', [
                'name' => 'Invalid',
                'proficiency' => 150,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['proficiency']);
    }

    // ====== EXPERIENCES ======

    #[Test]
    public function guests_can_list_experiences()
    {
        Experience::factory()->count(2)->create();

        $response = $this->getJson('/api/experiences');

        $response->assertOk();
        $this->assertCount(2, $response->json());
    }

    #[Test]
    public function admin_can_create_experience()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/experiences', [
                'role' => 'Developer',
                'company' => 'Tech Co',
                'start_date' => '2023',
                'end_date' => 'Present',
            ]);

        $response->assertCreated()
            ->assertJson([
                'role' => 'Developer',
                'company' => 'Tech Co',
            ]);
    }

    #[Test]
    public function experience_start_date_format_is_validated()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/experiences', [
                'role' => 'Dev',
                'company' => 'Co',
                'start_date' => 'invalid-date',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_date']);
    }

    // ====== SOCIALS ======

    #[Test]
    public function guests_can_list_socials()
    {
        Social::factory()->count(2)->create();

        $response = $this->getJson('/api/socials');

        $response->assertOk();
        $this->assertCount(2, $response->json());
    }

    #[Test]
    public function admin_can_create_social()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/socials', [
                'platform' => 'GitHub',
                'url' => 'https://github.com/test',
            ]);

        $response->assertCreated()
            ->assertJson(['platform' => 'GitHub']);
    }

    #[Test]
    public function social_url_must_be_valid()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/socials', [
                'platform' => 'GitHub',
                'url' => 'not-a-url',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['url']);
    }

    // ====== INTERESTS ======

    #[Test]
    public function guests_can_list_interests()
    {
        Interest::factory()->count(2)->create();

        $response = $this->getJson('/api/interests');

        $response->assertOk();
        $this->assertCount(2, $response->json());
    }

    #[Test]
    public function admin_can_create_interest()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/interests', [
                'name' => 'Photography',
                'icon' => '📷',
            ]);

        $response->assertCreated()
            ->assertJson(['name' => 'Photography']);
    }

    #[Test]
    public function admin_can_bulk_update_interests()
    {
        $token = $this->adminToken();
        $existing = Interest::factory()->create(['name' => 'Old Interest']);

        $response = $this->withToken($token)
            ->putJson('/api/interests-bulk', [
                'interests' => [
                    ['id' => $existing->id, 'name' => 'Updated Interest', 'icon' => '🆕'],
                    ['name' => 'New Interest', 'icon' => '✨'],
                ],
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('interests', ['name' => 'Updated Interest']);
        $this->assertDatabaseHas('interests', ['name' => 'New Interest']);
        $this->assertDatabaseMissing('interests', ['name' => 'Old Interest']);
    }

    #[Test]
    public function bulk_update_deletes_interests_not_in_list()
    {
        $token = $this->adminToken();
        Interest::factory()->create(['name' => 'To Be Deleted']);

        $response = $this->withToken($token)
            ->putJson('/api/interests-bulk', [
                'interests' => [
                    ['name' => 'Only This One'],
                ],
            ]);

        $response->assertOk();
        $this->assertDatabaseMissing('interests', ['name' => 'To Be Deleted']);
        $this->assertDatabaseHas('interests', ['name' => 'Only This One']);
    }

    // ====== GALLERY ======

    #[Test]
    public function guests_can_list_gallery()
    {
        GalleryImage::factory()->count(3)->create();

        $response = $this->getJson('/api/gallery');

        $response->assertOk();
        $this->assertCount(3, $response->json());
    }

    #[Test]
    public function admin_can_create_gallery_image()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/gallery', [
                'url' => 'https://example.com/image.jpg',
                'alt' => 'Test image',
                'category' => 'Nature',
            ]);

        $response->assertCreated();
        $this->assertCount(1, $response->json());
        $this->assertEquals('https://example.com/image.jpg', $response->json()[0]['url']);
    }

    #[Test]
    public function admin_can_create_multiple_gallery_images()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/gallery', [
                'items' => [
                    ['url' => 'https://example.com/img1.jpg', 'alt' => 'Image 1'],
                    ['url' => 'https://example.com/img2.jpg', 'alt' => 'Image 2'],
                ],
            ]);

        $response->assertCreated();
        $this->assertCount(2, $response->json());
    }

    #[Test]
    public function admin_can_bulk_delete_gallery_images()
    {
        $token = $this->adminToken();
        $img1 = GalleryImage::factory()->create();
        $img2 = GalleryImage::factory()->create();

        $response = $this->withToken($token)
            ->deleteJson('/api/gallery-bulk', [
                'ids' => [$img1->id, $img2->id],
            ]);

        $response->assertOk();
        $this->assertDatabaseMissing('gallery_images', ['id' => $img1->id]);
    }

    // ====== PROTECTED ENDPOINTS ======

    #[Test]
    public function guest_cannot_access_admin_endpoints()
    {
        $endpoints = [
            ['POST', '/api/projects'],
            ['PUT', '/api/projects/1'],
            ['DELETE', '/api/projects/1'],
            ['POST', '/api/skills'],
            ['PUT', '/api/skills/1'],
            ['POST', '/api/experiences'],
            ['PUT', '/api/experiences/1'],
            ['POST', '/api/socials'],
            ['PUT', '/api/socials/1'],
            ['POST', '/api/interests'],
            ['PUT', '/api/interests/1'],
            ['PUT', '/api/interests-bulk'],
            ['PUT', '/api/personal-info'],
            ['POST', '/api/gallery'],
            ['PUT', '/api/gallery/1'],
        ];

        foreach ($endpoints as [$method, $uri]) {
            $response = $this->json($method, $uri);
            $response->assertUnauthorized();
        }
    }

    #[Test]
    public function settings_are_public()
    {
        \App\Models\SiteSetting::create(['key' => 'site_name', 'value' => ['name' => 'Portfolio']]);

        $response = $this->getJson('/api/settings');

        $response->assertOk();
        $this->assertArrayHasKey('site_name', $response->json());
    }

    #[Test]
    public function admin_can_update_settings()
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->putJson('/api/settings', [
                'site_name' => 'My Portfolio',
                'allowPosts' => true,
                'maxLength' => 300,
            ]);

        $response->assertOk();
    }
}
