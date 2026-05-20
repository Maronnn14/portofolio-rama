<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MobileResponsivenessTest extends TestCase
{
    public function test_global_mobile_rules_prevent_viewport_overflow(): void
    {
        $reset = $this->readCss('reset.css');
        $global = $this->readCss('global.css');

        $this->assertStringContainsString('overflow-x: clip;', $reset);
        $this->assertStringContainsString('overflow-x: clip;', $global);
        $this->assertStringContainsString('@media (max-width: 640px)', $global);
        $this->assertStringContainsString('padding-inline: clamp(1rem, 5vw, 1.25rem);', $global);
    }

    public function test_shared_components_have_phone_first_overrides(): void
    {
        $components = $this->readCss('components.css');

        $this->assertStringContainsString('white-space: normal;', $components);
        $this->assertStringContainsString('.btn {', $components);
        $this->assertStringContainsString('.tag {', $components);
        $this->assertStringContainsString('@media (max-width: 640px)', $components);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $components);
    }

    public function test_home_and_admin_have_stronger_phone_layouts(): void
    {
        $home = $this->readCss('home.css');
        $admin = $this->readCss('admin.css');

        $this->assertStringContainsString('min-height: 100svh;', $home);
        $this->assertStringContainsString('font-size: clamp(2.25rem, 16vw, 3.25rem);', $home);
        $this->assertStringContainsString('width: min(86vw, 280px);', $admin);
        $this->assertStringContainsString('[style*="min-width:260px"]', $admin);
    }

    private function readCss(string $filename): string
    {
        return file_get_contents(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public/css/' . $filename);
    }
}
