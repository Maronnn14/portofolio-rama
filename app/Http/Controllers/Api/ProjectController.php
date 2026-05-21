<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = Project::orderBy('sort_order')->orderByDesc('created_at')->paginate(50);

        return response()->json($projects);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_desc' => ['required', 'string', 'max:500'],
            'full_desc' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:published,draft'],
            'thumbnail' => ['nullable', 'string'],
            'tech' => ['nullable', 'array'],
            'live_url' => ['nullable', 'string', 'max:500'],
            'source_url' => ['nullable', 'string', 'max:500'],
            'featured' => ['nullable', 'boolean'],
            'gallery' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $project = Project::create($validated);

        return response()->json($project, 201);
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json($project);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'short_desc' => ['sometimes', 'required', 'string', 'max:500'],
            'full_desc' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:published,draft'],
            'thumbnail' => ['nullable', 'string'],
            'tech' => ['nullable', 'array'],
            'live_url' => ['nullable', 'string', 'max:500'],
            'source_url' => ['nullable', 'string', 'max:500'],
            'featured' => ['nullable', 'boolean'],
            'gallery' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $project->update($validated);

        return response()->json($project);
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json(['message' => 'Project deleted']);
    }
}
