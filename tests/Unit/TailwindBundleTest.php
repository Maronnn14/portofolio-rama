<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TailwindBundleTest extends TestCase
{
    public function test_app_css_is_the_single_tailwind_entrypoint_for_site_styles(): void
    {
        $css = file_get_contents(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'resources/css/app.css');

        $this->assertStringContainsString("@import 'tailwindcss';", $css);

        foreach ([
            'variables.css',
            'reset.css',
            'global.css',
            'components.css',
            'admin-login.css',
            'lightbox.css',
            'home.css',
            'about.css',
            'skills.css',
            'projects.css',
            'contact.css',
            'admin.css',
        ] as $stylesheet) {
            $this->assertStringContainsString("../../public/css/{$stylesheet}", $css);
        }
    }

    public function test_blade_views_do_not_load_public_css_assets_directly(): void
    {
        $viewsPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'resources/views';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            $this->assertStringNotContainsString("asset('css", $contents, $file->getPathname());
            $this->assertStringNotContainsString('asset("css', $contents, $file->getPathname());
        }
    }
}
