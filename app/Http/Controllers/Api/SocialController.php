<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Social;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public function index(): JsonResponse
    {
        $socials = Social::orderBy('sort_order')->get();

        return response()->json($socials);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'platform' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:500'],
            'label' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'visible' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $social = Social::create($validated);

        return response()->json($social, 201);
    }

    public function show(Social $social): JsonResponse
    {
        return response()->json($social);
    }

    public function update(Request $request, Social $social): JsonResponse
    {
        $validated = $request->validate([
            'platform' => ['sometimes', 'required', 'string', 'max:100'],
            'url' => ['sometimes', 'required', 'string', 'max:500'],
            'label' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'visible' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $social->update($validated);

        return response()->json($social);
    }

    public function destroy(Social $social): JsonResponse
    {
        $social->delete();

        return response()->json(['message' => 'Social link deleted']);
    }
}
