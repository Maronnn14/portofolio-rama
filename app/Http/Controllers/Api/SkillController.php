<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\SkillGalleryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SkillController extends Controller
{
    public function index(): JsonResponse
    {
        $skills = Skill::with(['projectLinks', 'galleryItems'])
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

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
            'sort_order' => ['nullable', 'integer'],
        ]);

        $skill = Skill::create($validated);

        return response()->json($skill->load(['projectLinks', 'galleryItems']), 201);
    }

    public function show(Skill $skill): JsonResponse
    {
        return response()->json($skill->load(['projectLinks', 'galleryItems']));
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
            'sort_order' => ['nullable', 'integer'],
        ]);

        $skill->update($validated);

        return response()->json($skill->load(['projectLinks', 'galleryItems']));
    }

    public function destroy(Skill $skill): JsonResponse
    {
        $skill->delete();

        return response()->json(['message' => 'Skill deleted']);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer'],
        ]);

        Skill::whereIn('id', $validated['ids'])->each(fn ($skill) => $skill->delete());

        return response()->json(['message' => count($validated['ids']) . ' skills deleted']);
    }

    public function syncLinks(Request $request, Skill $skill): JsonResponse
    {
        $validated = $request->validate([
            'links' => ['required', 'array'],
            'links.*.label' => ['required', 'string', 'max:100'],
            'links.*.url' => ['required', 'url', 'max:500'],
            'links.*.description' => ['nullable', 'string', 'max:100'],
            'links.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $skill->projectLinks()->delete();

        $links = collect($validated['links'])->map(fn ($link, $i) => [
            'label' => $link['label'],
            'url' => $link['url'],
            'description' => $link['description'] ?? null,
            'sort_order' => $link['sort_order'] ?? $i,
        ]);

        $skill->projectLinks()->createMany($links->toArray());

        return response()->json($skill->load(['projectLinks', 'galleryItems']));
    }

    public function uploadGalleryImage(Request $request, Skill $skill): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,webp,gif', 'max:2048'],
        ]);

        $path = $validated['image']->store('skill-gallery', 'public');

        $item = $skill->galleryItems()->create([
            'image_path' => $path,
            'sort_order' => $skill->galleryItems()->max('sort_order') + 1,
        ]);

        return response()->json($item->fresh(), 201);
    }

    public function updateGallery(Request $request, Skill $skill): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:skill_gallery_items,id'],
            'items.*.caption' => ['nullable', 'string', 'max:150'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($validated['items'] as $data) {
            SkillGalleryItem::where('id', $data['id'])
                ->where('skill_id', $skill->id)
                ->update([
                    'caption' => $data['caption'] ?? null,
                    'sort_order' => $data['sort_order'] ?? 0,
                ]);
        }

        return response()->json($skill->load(['projectLinks', 'galleryItems']));
    }

    public function deleteGalleryItem(Skill $skill, SkillGalleryItem $item): JsonResponse
    {
        Storage::delete($item->image_path);
        $item->delete();

        return response()->json(['message' => 'Gallery item deleted']);
    }
}
