<?php

namespace App\Support;

use Illuminate\Support\Str;

class Icon
{
    private const DEVICON = [
        'html5', 'css3', 'javascript', 'typescript', 'react', 'vuejs', 'tailwindcss',
        'php', 'laravel', 'mysql', 'nodejs', 'python', 'git', 'docker', 'figma',
        'bootstrap', 'sass', 'jquery', 'nextjs', 'nuxtjs', 'postgresql', 'redis',
        'linux', 'nginx', 'aws', 'firebase', 'graphql', 'vite', 'webpack',
    ];

    public static function skill(?string $icon, string $name): ?string
    {
        if (blank($icon)) {
            return null;
        }

        if (Str::startsWith($icon, ['http://', 'https://', 'data:', '/'])) {
            return $icon;
        }

        if (in_array($icon, self::DEVICON, true)) {
            return "https://cdn.jsdelivr.net/gh/devicons/devicon/icons/{$icon}/{$icon}-original.svg";
        }

        return null;
    }

    public static function initials(string $name): string
    {
        return Str::of($name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');
    }
}
