<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index(): JsonResponse
    {
        $experiences = Experience::orderBy('sort_order')->orderByDesc('created_at')->get();

        return response()->json($experiences);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'string', 'max:50'],
            'end_date' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'tech' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $experience = Experience::create($validated);

        return response()->json($experience, 201);
    }

    public function show(Experience $experience): JsonResponse
    {
        return response()->json($experience);
    }

    public function update(Request $request, Experience $experience): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['sometimes', 'required', 'string', 'max:255'],
            'company' => ['sometimes', 'required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['sometimes', 'required', 'string', 'max:50'],
            'end_date' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'tech' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $experience->update($validated);

        return response()->json($experience);
    }

    public function destroy(Experience $experience): JsonResponse
    {
        $experience->delete();

        return response()->json(['message' => 'Experience deleted']);
    }
}
