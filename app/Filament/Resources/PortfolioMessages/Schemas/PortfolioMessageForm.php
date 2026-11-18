<?php

namespace App\Filament\Resources\PortfolioMessages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PortfolioMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),

                Select::make('rating')
                    ->options([
                        0 => 'No rating (0★)',
                        1 => '1 Star (★☆☆☆☆)',
                        2 => '2 Stars (★★☆☆☆)',
                        3 => '3 Stars (★★★☆☆)',
                        4 => '4 Stars (★★★★☆)',
                        5 => '5 Stars (★★★★★)',
                    ])
                    ->default(5)
                    ->required(),

                Textarea::make('message')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                Toggle::make('pinned')
                    ->label('Pinned to top'),

                Toggle::make('flagged')
                    ->label('Flagged / Reported'),

                Toggle::make('hidden')
                    ->label('Hidden from public'),
            ]);
    }
}
