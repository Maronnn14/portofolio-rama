<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(): JsonResponse
    {
        $images = GalleryImage::orderBy('sort_order')->orderByDesc('created_at')->paginate(50);

        return response()->json($images);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['sometimes', 'array'],
            'items.*.url' => ['required', 'string'],
            'items.*.alt' => ['nullable', 'string', 'max:255'],
            'items.*.category' => ['nullable', 'string', 'max:100'],
            'items.*.visible' => ['nullable', 'boolean'],
            // Single item creation
            'url' => ['sometimes', 'required_without:items', 'string'],
            'alt' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'visible' => ['nullable', 'boolean'],
        ]);

        $created = [];

        if (isset($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $created[] = GalleryImage::create($item);
            }
        } else {
            $created[] = GalleryImage::create($validated);
        }

        return response()->json($created, 201);
    }

    public function show(GalleryImage $gallery): JsonResponse
    {
        return response()->json($gallery);
    }

    public function update(Request $request, GalleryImage $gallery): JsonResponse
    {
        $validated = $request->validate([
            'url' => ['sometimes', 'string'],
            'alt' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'visible' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $gallery->update($validated);

        return response()->json($gallery);
    }

    public function destroy(GalleryImage $gallery): JsonResponse
    {
        $gallery->delete();

        return response()->json(['message' => 'Image deleted']);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer'],
        ]);

        GalleryImage::whereIn('id', $validated['ids'])->delete();

        return response()->json(['message' => count($validated['ids']) . ' images deleted']);
    }
}
