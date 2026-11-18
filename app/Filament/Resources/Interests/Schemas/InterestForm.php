<?php

namespace App\Filament\Resources\Interests\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InterestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Open Source, Cloud Architecture'),

                TextInput::make('icon')
                    ->default('🎯')
                    ->maxLength(50)
                    ->placeholder('Emoji or icon name, e.g. 🎯'),

                TextInput::make('description')
                    ->maxLength(255)
                    ->placeholder('Short description or context'),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
