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
        if (!$this->resolveAdminUser($request)) {
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
            'session_token' => ['nullable', 'string', 'max:255'],
        ]);

        $msg = PortfolioMessage::create([
            'message_id' => 'msg_' . time() . '_' . substr(md5(uniqid()), 0, 7),
            'name' => $validated['name'],
            'message' => $validated['message'],
            'rating' => $validated['rating'] ?? 0,
            'posted_at_ms' => (int) (microtime(true) * 1000),
            'session_token' => $validated['session_token'] ?? null,
            'hidden' => false,
            'flagged' => false,
            'pinned' => false,
        ]);

        return response()->json($msg->toFrontendArray(), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $msg = PortfolioMessage::where('message_id', $id)->firstOrFail();

        $isAdmin = (bool) $this->resolveAdminUser($request);

        if (!$isAdmin) {
            $sessionToken = $request->input('session_token');
            if (!$sessionToken || $sessionToken !== $msg->session_token) {
                return response()->json(['message' => 'You can only edit your own messages.'], 403);
            }
        }

        $rules = [
            'message' => ['sometimes', 'string', 'max:1000'],
        ];

        if ($isAdmin) {
            $rules['hidden'] = ['sometimes', 'boolean'];
            $rules['flagged'] = ['sometimes', 'boolean'];
            $rules['pinned'] = ['sometimes', 'boolean'];
        }

        $validated = $request->validate($rules);

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

        $isAdmin = (bool) $this->resolveAdminUser($request);

        if (!$isAdmin) {
            $sessionToken = $request->input('session_token');
            if (!$sessionToken || $sessionToken !== $msg->session_token) {
                return response()->json(['message' => 'You can only delete your own messages.'], 403);
            }
        }

        $msg->delete();

        return response()->json(['message' => 'Message deleted']);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'string'],
        ]);

        PortfolioMessage::whereIn('message_id', $validated['ids'])->delete();

        return response()->json(['message' => count($validated['ids']) . ' messages deleted']);
    }

    private function resolveAdminUser(Request $request)
    {
        if ($request->user()) {
            return $request->user();
        }

        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if ($accessToken && $accessToken->tokenable) {
            return $accessToken->tokenable;
        }

        return null;
    }
}
