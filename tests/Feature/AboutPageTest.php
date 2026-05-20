<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function test_about_page_loads_with_responsive_stylesheet(): void
    {
        $response = $this->get('/about.html');

        $response
            ->assertOk()
            ->assertSee('name="viewport"', false)
            ->assertSee('/build/assets/app-', false)
            ->assertDontSee('css/about.css', false);
    }

    public function test_about_page_contains_the_responsive_sections(): void
    {
        $response = $this->get('/about.html');

        $response
            ->assertOk()
            ->assertSee('about-story__grid', false)
            ->assertSee('about-story__image-wrapper', false)
            ->assertSee('timeline', false)
            ->assertSee('interests-grid', false);
    }
}
