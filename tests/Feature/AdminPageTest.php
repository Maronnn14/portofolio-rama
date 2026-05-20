<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminPageTest extends TestCase
{
    public function test_admin_dashboard_loads_with_expected_assets(): void
    {
        $response = $this->get('/admin.html');

        $response
            ->assertOk()
            ->assertSee('name="viewport"', false)
            ->assertSee('name="robots"', false)
            ->assertSee('/build/assets/app-', false)
            ->assertDontSee('css/admin.css', false)
            ->assertDontSee('css/admin-login.css', false);
    }

    public function test_admin_dashboard_contains_responsive_layout_sections(): void
    {
        $response = $this->get('/admin.html');

        $response
            ->assertOk()
            ->assertSee('admin-sidebar', false)
            ->assertSee('admin-main', false)
            ->assertSee('admin-topbar', false)
            ->assertSee('admin-content', false);
    }
}
