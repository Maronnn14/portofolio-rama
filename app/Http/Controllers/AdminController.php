<?php

namespace App\Http\Controllers;

use App\Models\PortfolioData;
use App\Models\PortfolioMessage;
use Illuminate\Database\QueryException;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', $this->databasePayload());
    }

    private function databasePayload(): array
    {
        try {
            return [
                'portfolioDatabaseData' => PortfolioData::query()->pluck('payload', 'section')->all(),
                'portfolioDatabaseMessages' => PortfolioMessage::query()
                    ->orderByDesc('pinned')
                    ->orderByDesc('posted_at_ms')
                    ->get()
                    ->map(fn (PortfolioMessage $message) => $message->toFrontendArray())
                    ->all(),
            ];
        } catch (QueryException) {
            return [
                'portfolioDatabaseData' => [],
                'portfolioDatabaseMessages' => [],
            ];
        }
    }
}
