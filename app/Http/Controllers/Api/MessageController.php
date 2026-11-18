<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PortfolioMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PortfolioMessage::orderByDesc('pinned')
            ->orderByDesc('posted_at_ms');

        // Public visitors see only visible messages; admins see all
        if (! $this->resolveAdminUser($request)) {
            $query->where('hidden', false);
        }

        $messages = $query->get()
            ->map(fn (PortfolioMessage $m) => $m->toFrontendArray());

        return response()->json($messages);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:1000'],
            'rating' => ['nullable', 'integer', 'min:0', 'max:5'],
        ]);

        $msg = PortfolioMessage::create([
            'message_id' => 'msg_'.time().'_'.substr(md5(uniqid()), 0, 7),
            'name' => $validated['name'],
            'message' => $validated['message'],
            'rating' => $validated['rating'] ?? 0,
            'posted_at_ms' => (int) (microtime(true) * 1000),
            // Tidak ada token dari klien: pesan tamu tidak bisa disunting sendiri.
            'session_token' => null,
            'hidden' => false,
            'flagged' => false,
            'pinned' => false,
        ]);

        return response()->json($msg->toFrontendArray(), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $msg = PortfolioMessage::where('message_id', $id)->firstOrFail();

        // Hanya admin yang boleh menyunting pesan tamu.
        if (! $this->resolveAdminUser($request)) {
            return response()->json(['message' => 'Only administrators can edit guestbook messages.'], 403);
        }

        $validated = $request->validate([
            'message' => ['sometimes', 'string', 'max:1000'],
            'hidden' => ['sometimes', 'boolean'],
            'flagged' => ['sometimes', 'boolean'],
            'pinned' => ['sometimes', 'boolean'],
        ]);

        if (isset($validated['message'])) {
            $msg->message = $validated['message'];
        }
        if (isset($validated['hidden'])) {
            $msg->setAttribute('hidden', $validated['hidden']);
        }
        if (isset($validated['flagged'])) {
            $msg->setAttribute('flagged', $validated['flagged']);
        }
        if (isset($validated['pinned'])) {
            $msg->setAttribute('pinned', $validated['pinned']);
        }

        $msg->save();

        return response()->json($msg->toFrontendArray());
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $msg = PortfolioMessage::where('message_id', $id)->firstOrFail();

        // Hanya admin yang boleh menghapus pesan tamu.
        if (! $this->resolveAdminUser($request)) {
            return response()->json(['message' => 'Only administrators can delete guestbook messages.'], 403);
        }

        $msg->delete();

        return response()->json(['message' => 'Message deleted']);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        if (! $this->resolveAdminUser($request)) {
            return response()->json(['message' => 'Only administrators can delete guestbook messages.'], 403);
        }

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'string'],
        ]);

        PortfolioMessage::whereIn('message_id', $validated['ids'])->delete();

        return response()->json(['message' => count($validated['ids']).' messages deleted']);
    }

    private function resolveAdminUser(Request $request)
    {
        if ($request->user()) {
            return $request->user();
        }

        $token = $request->bearerToken();
        if (! $token) {
            return null;
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if ($accessToken && $accessToken->tokenable) {
            return $accessToken->tokenable;
        }

        return null;
    }
}
