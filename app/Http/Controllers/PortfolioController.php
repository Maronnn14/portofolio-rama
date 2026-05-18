<?php

namespace App\Http\Controllers;

use App\Models\PortfolioData;
use App\Models\PortfolioMessage;
use Illuminate\Database\QueryException;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function home(): View
    {
        return view('portfolio.home', $this->databasePayload());
    }

    public function about(): View
    {
        return view('portfolio.about', $this->databasePayload());
    }

    public function projects(): View
    {
        return view('portfolio.projects', $this->databasePayload());
    }

    public function projectDetail(): View
    {
        return view('portfolio.project-detail', $this->databasePayload());
    }

    public function skills(): View
    {
        return view('portfolio.skills', $this->databasePayload());
    }

    public function contact(): View
    {
        return view('portfolio.contact', $this->databasePayload());
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
