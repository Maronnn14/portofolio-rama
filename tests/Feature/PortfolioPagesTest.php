<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PortfolioPagesTest extends TestCase
{
    #[DataProvider('pageProvider')]
    public function test_portfolio_pages_load_with_tailwind_bundle(string $path, array $sections): void
    {
        $response = $this->get($path);

        $response
            ->assertOk()
            ->assertSee('name="viewport"', false)
            ->assertSee('/build/assets/app-', false);

        foreach ($sections as $section) {
            $response->assertSee($section, false);
        }

        foreach (['css/home.css', 'css/about.css', 'css/skills.css', 'css/projects.css', 'css/contact.css', 'css/lightbox.css'] as $legacyStylesheet) {
            $response->assertDontSee($legacyStylesheet, false);
        }
    }

    public static function pageProvider(): array
    {
        return [
            'home' => [
                '/',
                ['hero', 'about-preview__grid', 'projects-preview__grid', 'gallery-grid'],
            ],
            'about' => [
                '/about.html',
                ['about-story__grid', 'timeline', 'interests-grid'],
            ],
            'skills' => [
                '/skills.html',
                ['filter-tabs', 'skills-grid', 'skill-modal'],
            ],
            'projects' => [
                '/projects.html',
                ['filter-tabs', 'projects-grid'],
            ],
            'project detail' => [
                '/project-detail.html',
                ['project-detail'],
            ],
            'contact' => [
                '/contact.html',
                ['contact-grid', 'contact-form', 'github-profile'],
            ],
        ];
    }
}
