<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonalInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonalInfoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(PersonalInfo::getAll());
    }

    private const ALLOWED_KEYS = [
        'name', 'nickname', 'fullName', 'role', 'tagline', 'shortBio', 'fullBio',
        'email', 'location', 'profileImage', 'github',
    ];

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'fullName' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:500'],
            'shortBio' => ['nullable', 'string', 'max:1000'],
            'fullBio' => ['nullable', 'string', 'max:10000'],
            'email' => ['nullable', 'email', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'profileImage' => ['nullable', 'string', 'max:500'],
            'github' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            if (in_array($key, self::ALLOWED_KEYS, true)) {
                PersonalInfo::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value ?? ''],
                );
            }
        }

        return response()->json(PersonalInfo::getAll());
    }
}
