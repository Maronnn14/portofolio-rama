<?php

namespace App\Support;

use App\Models\SiteSetting;

class Navigation
{
    public static function items(): array
    {
        $settings = SiteSetting::navigation();
        $labels = $settings['labels'];

        $definitions = [
            'home' => [
                'label' => $labels['home'],
                'url' => route('home'),
                'active' => request()->routeIs('home'),
                'children' => [],
            ],
            'about' => [
                'label' => $labels['about'],
                'url' => route('about.biodata'),
                'active' => request()->routeIs('about.*', 'about.html'),
                'children' => [
                    [
                        'label' => $labels['about_biodata'] ?? 'Biodata & Personal Branding',
                        'url' => route('about.biodata'),
                        'active' => request()->routeIs('about.biodata', 'about.html'),
                    ],
                    [
                        'label' => $labels['about_cv'] ?? 'Download CV',
                        'url' => route('about.cv'),
                        'active' => request()->routeIs('about.cv'),
                    ],
                    [
                        'label' => $labels['about_interests'] ?? 'Interests & Hobbies',
                        'url' => route('about.interests'),
                        'active' => request()->routeIs('about.interests'),
                    ],
                    [
                        'label' => $labels['about_blog'] ?? 'Daily Blog',
                        'url' => route('about.blog'),
                        'active' => request()->routeIs('about.blog', 'about.blog.show'),
                    ],
                ],
            ],
            'skills' => [
                'label' => $labels['skills'],
                'url' => route('skills'),
                'active' => request()->routeIs('skills', 'skills.html'),
                'children' => [],
            ],
            'projects' => [
                'label' => $labels['projects'],
                'url' => route('projects.index'),
                'active' => request()->routeIs('projects.*', 'projects.html', 'project.*', 'experience.*', 'contributions.*'),
                'children' => [
                    [
                        'label' => $labels['projects_coding'] ?? 'Coding Projects',
                        'url' => route('projects.index'),
                        'active' => request()->routeIs('projects.*', 'projects.html', 'project.*'),
                    ],
                    [
                        'label' => $labels['projects_experience'] ?? 'Work Experience',
                        'url' => route('experience.index'),
                        'active' => request()->routeIs('experience.*'),
                    ],
                    [
                        'label' => $labels['projects_contributions'] ?? 'Contributions & Activities',
                        'url' => route('contributions.index'),
                        'active' => request()->routeIs('contributions.*'),
                    ],
                ],
            ],
            'contact' => [
                'label' => $labels['contact'],
                'url' => route('contact'),
                'active' => request()->routeIs('contact', 'contact.html'),
                'children' => [],
            ],
        ];

        return collect($settings['order'])
            ->map(fn ($key) => $definitions[$key] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    public static function flat(): array
    {
        return collect(static::items())
            ->flatMap(fn ($item) => collect([$item])->merge($item['children']))
            ->values()
            ->all();
    }
}
