<?php

namespace App\Http\Controllers;

use App\Models\PortfolioData;
use App\Models\PortfolioMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortfolioDataController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'data' => $this->loadPortfolioData(),
            'messages' => $this->loadMessages(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'data' => ['required', 'array'],
        ])['data'];

        DB::transaction(function () use ($payload): void {
            foreach ($payload as $section => $data) {
                PortfolioData::updateOrCreate(
                    ['section' => $section],
                    ['payload' => $data],
                );
            }
        });

        return response()->json(['ok' => true]);
    }

    public function messages(): JsonResponse
    {
        return response()->json([
            'messages' => $this->loadMessages(),
        ]);
    }

    public function syncMessages(Request $request): JsonResponse
    {
        $messages = $request->validate([
            'messages' => ['required', 'array'],
            'messages.*.id' => ['required', 'string', 'max:120'],
            'messages.*.name' => ['required', 'string', 'max:100'],
            'messages.*.message' => ['required', 'string'],
            'messages.*.rating' => ['nullable', 'integer', 'min:0', 'max:5'],
            'messages.*.timestamp' => ['required', 'integer'],
            'messages.*.sessionToken' => ['nullable', 'string', 'max:255'],
            'messages.*.hidden' => ['nullable', 'boolean'],
            'messages.*.flagged' => ['nullable', 'boolean'],
            'messages.*.pinned' => ['nullable', 'boolean'],
        ])['messages'];

        DB::transaction(function () use ($messages): void {
            $keptIds = [];

            foreach ($messages as $message) {
                $keptIds[] = $message['id'];

                PortfolioMessage::updateOrCreate(
                    ['message_id' => $message['id']],
                    [
                        'name' => $message['name'],
                        'message' => $message['message'],
                        'rating' => $message['rating'] ?? 0,
                        'posted_at_ms' => $message['timestamp'],
                        'session_token' => $message['sessionToken'] ?? null,
                        'hidden' => $message['hidden'] ?? false,
                        'flagged' => $message['flagged'] ?? false,
                        'pinned' => $message['pinned'] ?? false,
                    ],
                );
            }

            PortfolioMessage::whereNotIn('message_id', $keptIds)->delete();
        });

        return response()->json(['ok' => true]);
    }

    private function loadPortfolioData(): array
    {
        return PortfolioData::query()
            ->pluck('payload', 'section')
            ->all();
    }

    private function loadMessages(): array
    {
        return PortfolioMessage::query()
            ->orderByDesc('pinned')
            ->orderByDesc('posted_at_ms')
            ->get()
            ->map(fn (PortfolioMessage $message) => $message->toFrontendArray())
            ->all();
    }
}
