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
    ];

    public function index(): JsonResponse
    {
        $settings = SiteSetting::pluck('value', 'key')->all();

        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $rules = [];
        foreach (self::ALLOWED_KEYS as $key) {
            $rules[$key] = ['nullable', 'string', 'max:500'];
        }

        $validated = $request->validate($rules);

        foreach ($validated as $key => $value) {
            if (in_array($key, self::ALLOWED_KEYS, true)) {
                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value ?? ''],
                );
            }
        }

        return response()->json(SiteSetting::pluck('value', 'key')->all());
    }
}
