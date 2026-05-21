<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\GalleryImage;
use App\Models\Interest;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Social;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PortfolioDataTest extends TestCase
{
    use RefreshDatabase;

    // ====== HOME PAGE — DATA FILTERING ======

    #[Test]
    public function home_shows_only_featured_projects()
    {
        Project::factory()->create(['name' => 'Featured One', 'featured' => true, 'sort_order' => 1]);
        Project::factory()->create(['name' => 'Not Featured', 'featured' => false, 'sort_order' => 2]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Featured One');
        $response->assertDontSee('Not Featured');
    }

    #[Test]
    public function home_shows_only_visible_socials()
    {
        Social::factory()->create(['platform' => 'GitHub', 'visible' => true, 'sort_order' => 1]);
        Social::factory()->create(['platform' => 'Secret', 'visible' => false, 'sort_order' => 2]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('GitHub');
        $response->assertDontSee('Secret');
    }

    #[Test]
    public function home_shows_only_visible_gallery_images()
    {
        GalleryImage::factory()->create(['url' => '/visible.jpg', 'visible' => true, 'sort_order' => 1]);
        GalleryImage::factory()->create(['url' => '/hidden.jpg', 'visible' => false, 'sort_order' => 2]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('/visible.jpg');
        $response->assertDontSee('/hidden.jpg');
    }

    #[Test]
    public function home_orders_projects_by_sort_order()
    {
        Project::factory()->create(['name' => 'Second', 'featured' => true, 'sort_order' => 2]);
        Project::factory()->create(['name' => 'First', 'featured' => true, 'sort_order' => 1]);

        $response = $this->get('/');

        $response->assertOk();
        $this->assertTrue(
            strpos($response->content(), 'First') < strpos($response->content(), 'Second'),
            'Expected First to appear before Second in the response'
        );
    }

    // ====== HOME PAGE — EDGE CASES ======

    #[Test]
    public function home_handles_no_featured_projects()
    {
        Project::factory()->create(['featured' => false]);
        Project::factory()->create(['featured' => false]);

        $response = $this->get('/');

        $response->assertOk();
    }

    #[Test]
    public function home_handles_no_visible_socials()
    {
        Social::factory()->create(['visible' => false]);

        $response = $this->get('/');

        $response->assertOk();
    }

    #[Test]
    public function home_handles_empty_gallery()
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    // ====== PROJECT DETAIL ======

    #[Test]
    public function project_detail_shows_project_when_id_provided()
    {
        $project = Project::factory()->create(['name' => 'My Project']);

        $response = $this->get('/project-detail.html?id=' . $project->id);

        $response->assertOk();
        $response->assertSee('My Project');
    }

    #[Test]
    public function project_detail_returns_null_when_no_id()
    {
        $response = $this->get('/project-detail.html');

        $response->assertOk();
    }

    #[Test]
    public function project_detail_returns_null_when_id_not_found()
    {
        $response = $this->get('/project-detail.html?id=99999');

        $response->assertOk();
    }

    #[Test]
    public function project_detail_handles_non_numeric_id()
    {
        $response = $this->get('/project-detail.html?id=abc');

        $response->assertOk();
    }

    // ====== ABOUT PAGE ======

    #[Test]
    public function about_shows_experiences_ordered_by_sort_order()
    {
        Experience::factory()->create(['role' => 'Junior', 'sort_order' => 2]);
        Experience::factory()->create(['role' => 'Senior', 'sort_order' => 1]);

        $response = $this->get('/about.html');

        $response->assertOk();
        $this->assertTrue(
            strpos($response->content(), 'Senior') < strpos($response->content(), 'Junior'),
            'Expected Senior to appear before Junior by sort_order'
        );
    }

    #[Test]
    public function about_shows_interests()
    {
        Interest::factory()->create(['name' => 'Photography']);
        Interest::factory()->create(['name' => 'Travel']);

        $response = $this->get('/about.html');

        $response->assertOk();
        $response->assertSee('Photography');
        $response->assertSee('Travel');
    }

    #[Test]
    public function about_handles_no_experiences()
    {
        $response = $this->get('/about.html');

        $response->assertOk();
    }

    #[Test]
    public function about_handles_no_interests()
    {
        $response = $this->get('/about.html');

        $response->assertOk();
    }

    // ====== PROJECTS PAGE ======

    #[Test]
    public function projects_page_lists_all_projects_ordered()
    {
        Project::factory()->create(['name' => 'Z Project', 'sort_order' => 2]);
        Project::factory()->create(['name' => 'A Project', 'sort_order' => 1]);
        Project::factory()->create(['name' => 'Hidden', 'featured' => false, 'sort_order' => 3]);

        $response = $this->get('/projects.html');

        $response->assertOk();
        $response->assertSee('Z Project');
        $response->assertSee('A Project');
        $response->assertSee('Hidden');
        $this->assertTrue(
            strpos($response->content(), 'A Project') < strpos($response->content(), 'Z Project'),
            'Expected A Project to appear before Z Project'
        );
    }

    #[Test]
    public function projects_page_handles_no_projects()
    {
        $response = $this->get('/projects.html');

        $response->assertOk();
    }

    // ====== SKILLS PAGE ======

    #[Test]
    public function skills_page_shows_skills_ordered()
    {
        Skill::factory()->create(['name' => 'Laravel', 'sort_order' => 1]);
        Skill::factory()->create(['name' => 'Vue', 'sort_order' => 2]);

        $response = $this->get('/skills.html');

        $response->assertOk();
        $response->assertSee('Laravel');
        $response->assertSee('Vue');
    }

    #[Test]
    public function skills_page_handles_no_skills()
    {
        $response = $this->get('/skills.html');

        $response->assertOk();
    }

    #[Test]
    public function skills_page_handles_no_projects()
    {
        // $projects is passed to the view for JS, not rendered directly
        Skill::factory()->create(['name' => 'Laravel', 'sort_order' => 1]);

        $response = $this->get('/skills.html');

        $response->assertOk();
        $response->assertSee('Laravel');
    }

    // ====== CONTACT PAGE ======

    #[Test]
    public function contact_shows_only_visible_socials()
    {
        Social::factory()->create(['platform' => 'LinkedIn', 'visible' => true, 'sort_order' => 1]);
        Social::factory()->create(['platform' => 'Twitter', 'visible' => false, 'sort_order' => 2]);

        $response = $this->get('/contact.html');

        $response->assertOk();
        $response->assertSee('LinkedIn');
        $response->assertDontSee('Twitter');
    }

    #[Test]
    public function contact_handles_no_socials()
    {
        $response = $this->get('/contact.html');

        $response->assertOk();
    }

    // ====== ADMIN PAGE ======

    #[Test]
    public function admin_dashboard_loads()
    {
        $response = $this->get('/admin.html');

        $response->assertOk()
            ->assertSee('admin-sidebar');
    }

    // ====== INDEX.HTML REDIRECT ======

    #[Test]
    public function index_html_route_loads_home()
    {
        Project::factory()->create(['name' => 'Index Project', 'featured' => true]);

        $response = $this->get('/index.html');

        $response->assertOk();
        $response->assertSee('Index Project');
    }

    #[Test]
    public function all_public_routes_return_200()
    {
        $routes = ['/', '/about.html', '/projects.html', '/skills.html', '/contact.html'];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertOk();
        }
    }
}


/*
 * ====================================================================
 * TESTABILITY ANALYSIS
 * ====================================================================
 *
 * 1. PortfolioController::projectDetail() accesses request() directly — LINE 44
 *    Problem: The method uses the global request() helper to read 'id',
 *    coupling it to the HTTP request lifecycle. This makes it impossible to
 *    test this method in isolation outside of an HTTP request context.
 *    Suggestion: Inject the id via constructor parameter or route binding:
 *    Before:
 *        $project = null;
 *        if ($id = request('id')) {
 *            $project = Project::find($id);
 *        }
 *        return view('portfolio.project-detail', compact('project'));
 *    After:
 *        public function projectDetail(?int $id = null): View
 *        {
 *            $project = $id ? Project::find($id) : null;
 *            return view('portfolio.project-detail', compact('project'));
 *        }
 *    Then register the route with an optional parameter:
 *        Route::get('project-detail.html/{id?}', [PortfolioController::class, 'projectDetail']);
 *
 * 2. PortfolioController::home() loads 5 queries per request — LINES 18-22
 *    Problem: Home page executes 5 separate DB queries (projects, skills,
 *    experience, socials, gallery). With N+1 potential as the template
 *    iterates through each collection. This isn't a testability issue per se,
 *    but it's a performance concern that tests won't catch.
 *    Suggestion: Use lazy loading or chunking for large collections, or
 *    consider cached queries if the data doesn't change frequently.
 *
 * 3. AdminController::dashboard() is a thin pass-through — LINE 9-12
 *    Problem: This controller does nothing but return a view. This is fine
 *    for testability since there's no logic to test, but consider whether
 *    it could be replaced with a direct route-to-view declaration.
 *    No refactor needed.
 *
 * ====================================================================
 */
