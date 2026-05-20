<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PageResponsiveStylesTest extends TestCase
{
    #[DataProvider('responsiveStylesheetProvider')]
    public function test_page_stylesheets_include_responsive_breakpoints(string $stylesheet, array $breakpoints): void
    {
        $css = $this->readStylesheet($stylesheet);

        foreach ($breakpoints as $breakpoint) {
            $this->assertStringContainsString("@media (max-width: {$breakpoint})", $css);
        }
    }

    public function test_home_stylesheet_uses_mobile_safe_grids(): void
    {
        $css = $this->readStylesheet('home.css');

        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 110px), 1fr));', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr));', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr));', $css);
        $this->assertStringContainsString('min-width: 0;', $css);
    }

    public function test_skills_stylesheet_uses_mobile_safe_cards_and_modal(): void
    {
        $css = $this->readStylesheet('skills.css');

        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr));', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr));', $css);
        $this->assertStringContainsString('flex-wrap: wrap;', $css);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $css);
    }

    public function test_projects_stylesheet_uses_mobile_safe_project_layouts(): void
    {
        $css = $this->readStylesheet('projects.css');

        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr));', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr));', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));', $css);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $css);
    }

    public function test_contact_stylesheet_uses_mobile_safe_contact_layouts(): void
    {
        $css = $this->readStylesheet('contact.css');

        $this->assertStringContainsString('grid-template-columns: minmax(280px, 380px) minmax(0, 1fr);', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 90px), 1fr));', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));', $css);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $css);
    }

    public static function responsiveStylesheetProvider(): array
    {
        return [
            'home' => ['home.css', ['1024px', '768px', '480px']],
            'about' => ['about.css', ['1024px', '768px', '480px', '360px']],
            'skills' => ['skills.css', ['768px', '480px']],
            'projects' => ['projects.css', ['768px', '480px']],
            'contact' => ['contact.css', ['900px', '480px']],
        ];
    }

    private function readStylesheet(string $stylesheet): string
    {
        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public/css/' . $stylesheet;

        $this->assertFileExists($path);

        return file_get_contents($path);
    }
}
