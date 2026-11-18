<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_filament_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_legacy_admin_html_redirects_to_filament_admin(): void
    {
        $response = $this->get('/admin.html');

        $response->assertRedirect('/admin');
    }

    public function test_filament_login_page_loads_successfully(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
    }

    public function test_authenticated_admin_can_access_filament_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('Portfolio Rama');
    }

    public function test_authenticated_admin_can_access_filament_resources(): void
    {
        $admin = User::factory()->create();

        $routes = [
            '/admin/projects',
            '/admin/skills',
            '/admin/experiences',
            '/admin/socials',
            '/admin/interests',
            '/admin/gallery-images',
            '/admin/portfolio-messages',
            '/admin/contact-submissions',
            '/admin/profile',
            '/admin/appearance',
            '/admin/settings',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertOk();
        }
    }
}
