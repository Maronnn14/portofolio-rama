<?php

namespace Tests\Feature;

use App\Models\Contribution;
use App\Models\Experience;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function sitemap_lists_static_pages(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<urlset', false);
        $response->assertSee(route('home'), false);
        $response->assertSee(route('skills'), false);
        $response->assertSee(route('contact'), false);
    }

    #[Test]
    public function sitemap_includes_published_articles_only(): void
    {
        $published = Project::factory()->create([
            'name' => 'Published Project',
            'slug' => 'published-project',
            'status' => 'published',
        ]);
        $draft = Project::factory()->create([
            'name' => 'Draft Project',
            'slug' => 'draft-project',
            'status' => 'draft',
        ]);

        Experience::create([
            'role' => 'Engineer',
            'company' => 'Acme',
            'slug' => 'engineer-acme',
            'type' => 'work',
            'status' => 'published',
            'start_date' => '2023-01-01',
            'sort_order' => 1,
        ]);

        Contribution::create([
            'name' => 'Community Lead',
            'organization' => 'Dev Community',
            'slug' => 'community-lead',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertSee(route('projects.show', $published), false);
        $response->assertDontSee(route('projects.show', $draft), false);
        $response->assertSee(route('experience.index'), false);
        $response->assertSee(route('contributions.index'), false);
    }

    #[Test]
    public function robots_txt_points_to_the_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: '.route('sitemap'));
    }

    #[Test]
    public function missing_pages_render_the_designed_404(): void
    {
        $this->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('Page')
            ->assertSee('Not Found')
            ->assertSee('noindex, follow', false);
    }
}
