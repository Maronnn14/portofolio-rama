<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use App\Models\Contribution;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Interest;
use App\Models\Project;
use App\Models\Skill;
use App\Models\SoftSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PortfolioContentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function biodata_page_shows_branding_and_education_timeline(): void
    {
        Education::create([
            'institution' => 'Test University',
            'program' => 'Computer Science',
            'start_year' => '2020',
            'end_year' => '2024',
            'sort_order' => 1,
        ]);
        SoftSkill::create(['name' => 'Communication', 'description' => 'Clear updates.']);

        $this->get('/about/biodata')
            ->assertOk()
            ->assertSee('Test University')
            ->assertSee('Computer Science')
            ->assertSee('Communication');
    }

    #[Test]
    public function cv_page_loads_and_handles_missing_file(): void
    {
        $this->get('/about/cv')
            ->assertOk()
            ->assertSee('Download')
            ->assertSee('CV file has not been uploaded');
    }

    #[Test]
    public function interests_page_lists_interests(): void
    {
        Interest::factory()->create(['name' => 'Photography']);
        Interest::factory()->create(['name' => 'Travel']);

        $this->get('/about/interests')
            ->assertOk()
            ->assertSee('Photography')
            ->assertSee('Travel');
    }

    #[Test]
    public function skills_page_shows_hard_soft_skills_and_proficiency(): void
    {
        Skill::factory()->create(['name' => 'Laravel', 'proficiency' => 90, 'category' => 'Backend']);
        SoftSkill::create(['name' => 'Teamwork', 'description' => 'Pairs well.']);

        $this->get('/skills')
            ->assertOk()
            ->assertSee('Laravel')
            ->assertSee('Teamwork')
            ->assertSee('Proficiency 90 percent', false);
    }

    #[Test]
    public function skills_page_separates_tech_stack_from_other_hard_skills(): void
    {
        Skill::factory()->create(['name' => 'Laravel', 'category' => 'Backend', 'skill_type' => 'tech']);
        Skill::factory()->create(['name' => 'Microsoft Excel', 'category' => 'Spreadsheet', 'skill_type' => 'other']);
        SoftSkill::create(['name' => 'Teamwork', 'description' => 'Pairs well.']);

        $this->get('/skills')
            ->assertOk()
            ->assertSee('Tech Stack')
            ->assertSee('Other Skills')
            ->assertSee('Microsoft Excel')
            ->assertSee('Spreadsheet')
            ->assertSee('Teamwork');
    }

    #[Test]
    public function project_detail_page_is_rendered_as_an_article_from_slug(): void
    {
        $project = Project::factory()->create([
            'name' => 'Article Project',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'body' => "## The Challenge\n\nWe shipped it.",
        ]);

        $this->get('/projects/'.$project->slug)
            ->assertOk()
            ->assertSee('Article Project')
            ->assertSee('The Challenge')
            ->assertSee('We shipped it.');
    }

    #[Test]
    public function draft_projects_are_not_publicly_visible(): void
    {
        $project = Project::factory()->create([
            'name' => 'Draft Project',
            'status' => 'draft',
        ]);

        $this->get('/projects/'.$project->slug)->assertNotFound();
        $this->get('/projects')->assertOk()->assertDontSee('Draft Project');
    }

    #[Test]
    public function experience_index_and_detail_pages_work(): void
    {
        $experience = Experience::factory()->create([
            'role' => 'Backend Engineer',
            'company' => 'Acme',
            'status' => 'published',
            'body' => "### Impact\n\nCut latency in half.",
            'achievements' => ['Cut latency in half'],
        ]);

        $this->get('/experience')
            ->assertOk()
            ->assertSee('Backend Engineer')
            ->assertSee('Acme');

        $this->get('/experience/'.$experience->slug)
            ->assertOk()
            ->assertSee('Impact')
            ->assertSee('Cut latency in half');
    }

    #[Test]
    public function contributions_index_and_detail_pages_work(): void
    {
        $contribution = Contribution::create([
            'name' => 'Community Guild',
            'role' => 'Mentor',
            'organization' => 'Guild',
            'description' => 'Mentored builders.',
            'body' => '## Mentoring',
            'links' => [['label' => 'Profile', 'url' => 'https://example.com/profile']],
            'status' => 'published',
        ]);

        $this->get('/contributions')
            ->assertOk()
            ->assertSee('Community Guild')
            ->assertSee('Guild');

        $this->get('/contributions/'.$contribution->slug)
            ->assertOk()
            ->assertSee('Mentoring')
            ->assertSee('https://example.com/profile');
    }

    #[Test]
    public function contact_form_stores_a_submission(): void
    {
        $this->post('/contact', [
            'name' => 'Recruiter',
            'email' => 'recruiter@example.com',
            'subject' => 'Role opportunity',
            'message' => 'We would love to talk about a backend role.',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_submissions', [
            'email' => 'recruiter@example.com',
            'subject' => 'Role opportunity',
        ]);

        $this->assertNotNull(ContactSubmission::query()->first()?->ip_address);
    }

    #[Test]
    public function contact_form_validates_required_fields(): void
    {
        $this->from('/contact')
            ->post('/contact', ['name' => '', 'email' => 'not-an-email', 'message' => 'short'])
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    #[Test]
    public function all_navigation_targets_respond_successfully(): void
    {
        $routes = [
            '/',
            '/about/biodata',
            '/about/cv',
            '/about/interests',
            '/skills',
            '/projects',
            '/experience',
            '/contributions',
            '/contact',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertOk();
        }
    }
}
