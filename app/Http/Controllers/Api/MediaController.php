<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MediaController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            'folder' => ['nullable', 'string', Rule::in(['profile', 'gallery', 'projects'])],
        ]);

        $folder = $validated['folder'] ?? 'profile';
        $path = $request->file('image')->store("uploads/{$folder}", 'public');

        return response()->json([
            'path' => $path,
            'url' => '/storage/' . $path,
        ], 201);
    }
}
