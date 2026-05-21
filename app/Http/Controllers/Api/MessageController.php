<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PortfolioMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(): JsonResponse
    {
        $messages = PortfolioMessage::orderByDesc('pinned')
            ->orderByDesc('posted_at_ms')
            ->paginate(50)
            ->through(fn (PortfolioMessage $m) => $m->toFrontendArray());

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
            $msg->hidden = $validated['hidden'];
        }
        if (isset($validated['flagged'])) {
            $msg->flagged = $validated['flagged'];
        }
        if (isset($validated['pinned'])) {
            $msg->pinned = $validated['pinned'];
        }

        $msg->save();

        return response()->json($msg->toFrontendArray());
    }

    public function destroy(string $id): JsonResponse
    {
        $msg = PortfolioMessage::where('message_id', $id)->firstOrFail();
        $msg->delete();

        return response()->json(['message' => 'Message deleted']);
    }

    /**
     * Bulk delete messages by IDs.
     */
    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'string'],
        ]);

        PortfolioMessage::whereIn('message_id', $validated['ids'])->delete();

        return response()->json(['message' => count($validated['ids']) . ' messages deleted']);
    }
}
