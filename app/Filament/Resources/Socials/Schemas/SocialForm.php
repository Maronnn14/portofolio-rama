<?php

namespace App\Filament\Resources\Socials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SocialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('platform')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('GitHub, LinkedIn, Twitter, etc.'),

                TextInput::make('url')
                    ->required()
                    ->url()
                    ->maxLength(500)
                    ->placeholder('https://...'),

                TextInput::make('label')
                    ->maxLength(255)
                    ->placeholder('Optional display label'),

                TextInput::make('icon')
                    ->maxLength(255)
                    ->placeholder('Icon name or SVG path / class'),

                Toggle::make('visible')
                    ->default(true)
                    ->required(),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
