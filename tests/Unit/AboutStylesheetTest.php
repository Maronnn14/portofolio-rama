<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AboutStylesheetTest extends TestCase
{
    private string $css;

    protected function setUp(): void
    {
        parent::setUp();

        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public/css/about.css';

        $this->assertFileExists($path);
        $this->css = file_get_contents($path);
    }

    public function test_about_stylesheet_defines_responsive_breakpoints(): void
    {
        $this->assertStringContainsString('@media (max-width: 1024px)', $this->css);
        $this->assertStringContainsString('@media (max-width: 768px)', $this->css);
        $this->assertStringContainsString('@media (max-width: 480px)', $this->css);
        $this->assertStringContainsString('@media (max-width: 360px)', $this->css);
    }

    public function test_story_layout_collapses_and_prevents_horizontal_overflow(): void
    {
        $this->assertStringContainsString('grid-template-columns: minmax(260px, 350px) minmax(0, 1fr);', $this->css);
        $this->assertStringContainsString('grid-template-columns: 1fr;', $this->css);
        $this->assertStringContainsString('max-width: min(320px, calc(100vw - (var(--container-padding) * 2) - 24px));', $this->css);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $this->css);
    }

    public function test_timeline_and_card_grids_have_mobile_safe_rules(): void
    {
        $this->assertStringContainsString('.timeline .tag', $this->css);
        $this->assertStringContainsString('white-space: normal;', $this->css);
        $this->assertStringContainsString('width: 100%;', $this->css);
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 250px), 1fr));', $this->css);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $this->css);
    }
}
