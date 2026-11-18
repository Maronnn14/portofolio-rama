<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    public static function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function navigation(): array
    {
        $saved = static::getSetting('navigation', []) ?? [];

        $defaults = [
            'order' => ['home', 'about', 'skills', 'projects', 'contact'],
            'labels' => [
                'home' => 'Home',
                'about' => 'About Me',
                'skills' => 'Skills',
                'projects' => 'Projects & Experience',
                'contact' => 'Contact',
            ],
        ];

        $order = array_values(array_filter(
            $saved['order'] ?? $defaults['order'],
            fn ($key) => array_key_exists($key, $defaults['labels']),
        ));

        foreach (array_keys($defaults['labels']) as $key) {
            if (! in_array($key, $order, true)) {
                $order[] = $key;
            }
        }

        return [
            'order' => $order,
            'labels' => array_merge($defaults['labels'], array_filter($saved['labels'] ?? [])),
        ];
    }

    public static function home(): array
    {
        return array_merge([
            'hero_cta_primary_label' => 'View Projects',
            'hero_cta_primary_url' => '/projects',
            'hero_cta_secondary_label' => 'Contact Me',
            'hero_cta_secondary_url' => '/contact',
            'about_teaser' => null,
            'contact_cta_heading' => "Let's Work Together",
            'contact_cta_text' => 'Have a project in mind or want to collaborate? I would love to hear from you.',
        ], static::getSetting('home', []) ?? []);
    }

    public static function general(): array
    {
        return array_merge([
            'site_title' => 'Portfolio',
            'meta_description' => 'Personal portfolio, projects, and experience.',
            'og_image' => null,
            'footer_text' => null,
            'copyright_text' => null,
            'contact_email' => null,
            'availability' => null,
        ], static::getSetting('general', []) ?? []);
    }
}
