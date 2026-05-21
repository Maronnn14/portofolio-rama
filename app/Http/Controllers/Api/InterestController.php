<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function index(): JsonResponse
    {
        $interests = Interest::orderBy('sort_order')->get();

        return response()->json($interests);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $interest = Interest::create($validated);

        return response()->json($interest, 201);
    }

    public function show(Interest $interest): JsonResponse
    {
        return response()->json($interest);
    }

    public function update(Request $request, Interest $interest): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $interest->update($validated);

        return response()->json($interest);
    }

    public function destroy(Interest $interest): JsonResponse
    {
        $interest->delete();

        return response()->json(['message' => 'Interest deleted']);
    }

    /**
     * Bulk upsert interests (used from the profile page).
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'interests' => ['required', 'array'],
            'interests.*.id' => ['nullable', 'integer'],
            'interests.*.name' => ['required', 'string', 'max:255'],
            'interests.*.icon' => ['nullable', 'string', 'max:50'],
            'interests.*.description' => ['nullable', 'string', 'max:500'],
        ]);

        $keptIds = [];

        foreach ($validated['interests'] as $index => $data) {
            if (!empty($data['id'])) {
                $interest = Interest::find($data['id']);
                if ($interest) {
                    $interest->update([
                        'name' => $data['name'],
                        'icon' => $data['icon'] ?? '🎯',
                        'description' => $data['description'] ?? '',
                        'sort_order' => $index,
                    ]);
                    $keptIds[] = $interest->id;
                    continue;
                }
            }

            $interest = Interest::create([
                'name' => $data['name'],
                'icon' => $data['icon'] ?? '🎯',
                'description' => $data['description'] ?? '',
                'sort_order' => $index,
            ]);
            $keptIds[] = $interest->id;
        }

        // Delete interests not in the new list
        Interest::whereNotIn('id', $keptIds)->delete();

        return response()->json(Interest::orderBy('sort_order')->get());
    }
}
