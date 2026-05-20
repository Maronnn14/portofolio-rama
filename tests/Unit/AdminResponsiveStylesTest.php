<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AdminResponsiveStylesTest extends TestCase
{
    private string $css;

    protected function setUp(): void
    {
        parent::setUp();

        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public/css/admin.css';

        $this->assertFileExists($path);
        $this->css = file_get_contents($path);
    }

    public function test_admin_stylesheet_defines_responsive_breakpoints(): void
    {
        $this->assertStringContainsString('@media (max-width: 1024px)', $this->css);
        $this->assertStringContainsString('@media (max-width: 768px)', $this->css);
        $this->assertStringContainsString('@media (max-width: 480px)', $this->css);
    }

    public function test_admin_layout_uses_mobile_safe_grids_and_forms(): void
    {
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr));', $this->css);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $this->css);
        $this->assertStringContainsString('min-width: 0;', $this->css);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $this->css);
    }

    public function test_admin_tables_and_modals_are_overflow_safe(): void
    {
        $this->assertStringContainsString('overflow-x: auto;', $this->css);
        $this->assertStringContainsString('min-width: 640px;', $this->css);
        $this->assertStringContainsString('width: min(100% - (var(--space-md) * 2), 600px);', $this->css);
        $this->assertStringContainsString('max-height: calc(100vh - (var(--space-md) * 2));', $this->css);
    }
}
