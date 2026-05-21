<?php

namespace App\Providers;

use App\Models\PersonalInfo;
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
        } catch (\Exception $e) {
            $personal = [];
            $socials = collect([]);
        }

        View::share(compact('personal', 'socials'));
    }
}
