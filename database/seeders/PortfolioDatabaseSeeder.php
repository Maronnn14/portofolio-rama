<?php

namespace Database\Seeders;

use App\Models\PortfolioData;
use App\Models\PortfolioMessage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PortfolioDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/portfolio-data.json');
        $data = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);

        foreach ($data as $section => $payload) {
            PortfolioData::updateOrCreate(
                ['section' => $section],
                ['payload' => $payload],
            );
        }

        foreach ($data['seedMessages'] ?? [] as $index => $message) {
            PortfolioMessage::updateOrCreate(
                ['message_id' => 'seed_'.$index],
                [
                    'name' => $message['name'],
                    'message' => $message['message'],
                    'rating' => $message['rating'] ?? 0,
                    'posted_at_ms' => $message['timestamp'],
                    'session_token' => 'seed_visitor_'.$index,
                    'hidden' => false,
                    'flagged' => false,
                    'pinned' => false,
                ],
            );
        }
    }
}
