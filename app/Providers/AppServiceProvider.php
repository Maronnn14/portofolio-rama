<?php

namespace App\Providers;

use App\Models\PersonalInfo;
use App\Models\SiteSetting;
use App\Models\Social;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            $personal = PersonalInfo::getAll();
            $socials = Social::where('visible', true)->orderBy('sort_order')->get();
            $siteSettings = SiteSetting::general();
            $homeSettings = SiteSetting::home();
        } catch (\Throwable) {
            $personal = [];
            $socials = collect();
            $siteSettings = [
                'site_title' => 'Portfolio',
                'meta_description' => 'Personal portfolio, projects, and experience.',
                'og_image' => null,
                'footer_text' => null,
                'copyright_text' => null,
                'contact_email' => null,
                'availability' => null,
            ];
            $homeSettings = [];
        }

        View::share(compact('personal', 'socials', 'siteSettings', 'homeSettings'));
    }
}
