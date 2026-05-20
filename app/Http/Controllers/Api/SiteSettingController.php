<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = SiteSetting::pluck('value', 'key')->all();

        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        foreach ($request->all() as $key => $value) {
            if (is_string($key)) {
                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value],
                );
            }
        }

        return response()->json(SiteSetting::pluck('value', 'key')->all());
    }
}
