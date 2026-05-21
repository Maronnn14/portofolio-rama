<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    private const ALLOWED_KEYS = [
        'site_name', 'site_description', 'meta_keywords', 'meta_author',
        'google_analytics_id', 'footer_text', 'copyright_text',
        'siteTitle', 'metaDesc',
        'allowPosts', 'moderationMode', 'maxLength',
    ];

    private const BOOLEAN_KEYS = [
        'allowPosts', 'moderationMode',
    ];

    private const INTEGER_KEYS = [
        'maxLength',
    ];

    public function index(): JsonResponse
    {
        $settings = SiteSetting::pluck('value', 'key')->all();

        // Cast to proper types for the frontend
        foreach ($settings as $key => &$value) {
            if (in_array($key, self::BOOLEAN_KEYS, true)) {
                $value = (bool) $value;
            } elseif (in_array($key, self::INTEGER_KEYS, true)) {
                $value = (int) $value;
            }
        }

        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $rules = [];
        foreach (self::ALLOWED_KEYS as $key) {
            if (in_array($key, self::BOOLEAN_KEYS, true)) {
                $rules[$key] = ['nullable', 'boolean'];
            } elseif (in_array($key, self::INTEGER_KEYS, true)) {
                $rules[$key] = ['nullable', 'integer'];
            } else {
                $rules[$key] = ['nullable', 'string', 'max:500'];
            }
        }

        $validated = $request->validate($rules);

        foreach ($validated as $key => $value) {
            if (in_array($key, self::ALLOWED_KEYS, true)) {
                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) ($value ?? '')],
                );
            }
        }

        return response()->json(SiteSetting::pluck('value', 'key')->all());
    }
}
