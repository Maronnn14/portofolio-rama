<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonalInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'quote' => $this->getPI('about_quote', ''),
            'stats' => [
                $this->buildStat(1),
                $this->buildStat(2),
                $this->buildStat(3),
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quote'       => ['required', 'string', 'max:300'],
            'stats'       => ['required', 'array', 'size:3'],
            'stats.*.value' => ['required', 'string', 'max:20'],
            'stats.*.label' => ['required', 'string', 'max:50'],
        ]);

        PersonalInfo::updateOrCreate(
            ['key' => 'about_quote'],
            ['value' => $validated['quote']],
        );

        foreach ($validated['stats'] as $i => $stat) {
            $n = $i + 1;
            PersonalInfo::updateOrCreate(
                ['key' => "stat_{$n}_value"],
                ['value' => $stat['value']],
            );
            PersonalInfo::updateOrCreate(
                ['key' => "stat_{$n}_label"],
                ['value' => $stat['label']],
            );
        }

        return $this->index();
    }

    private function buildStat(int $num): array
    {
        return [
            'value' => $this->getPI("stat_{$num}_value", ''),
            'label' => $this->getPI("stat_{$num}_label", ''),
        ];
    }

    private function getPI(string $key, string $default): string
    {
        return PersonalInfo::where('key', $key)->value('value') ?? $default;
    }
}
