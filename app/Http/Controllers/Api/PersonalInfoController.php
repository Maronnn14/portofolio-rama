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

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            '*' => ['nullable', 'string'],
        ]);

        foreach ($request->all() as $key => $value) {
            if (is_string($key)) {
                PersonalInfo::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value ?? ''],
                );
            }
        }

        return response()->json(PersonalInfo::getAll());
    }
}
