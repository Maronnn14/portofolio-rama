<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index(): JsonResponse
    {
        $skills = Skill::orderBy('sort_order')->orderByDesc('created_at')->paginate(100);

        return response()->json($skills);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'teaser' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'proficiency' => ['nullable', 'integer', 'min:0', 'max:100'],
            'level' => ['nullable', 'string', 'max:50'],
            'related_projects' => ['nullable', 'array'],
            'gallery' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $skill = Skill::create($validated);

        return response()->json($skill, 201);
    }

    public function show(Skill $skill): JsonResponse
    {
        return response()->json($skill);
    }

    public function update(Request $request, Skill $skill): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'teaser' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'proficiency' => ['nullable', 'integer', 'min:0', 'max:100'],
            'level' => ['nullable', 'string', 'max:50'],
            'related_projects' => ['nullable', 'array'],
            'gallery' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $skill->update($validated);

        return response()->json($skill);
    }

    public function destroy(Skill $skill): JsonResponse
    {
        $skill->delete();

        return response()->json(['message' => 'Skill deleted']);
    }
}
