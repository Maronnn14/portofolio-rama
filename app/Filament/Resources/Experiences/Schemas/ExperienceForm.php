<?php

namespace App\Filament\Resources\Experiences\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('role')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Full Stack Developer'),

                TextInput::make('company')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Company or Organization name'),

                TextInput::make('location')
                    ->maxLength(255)
                    ->placeholder('e.g. Jakarta, Indonesia or Remote'),

                Select::make('type')
                    ->options([
                        'Work' => 'Work',
                        'Education' => 'Education',
                        'Organization' => 'Organization',
                    ])
                    ->default('Work')
                    ->required(),

                TextInput::make('start_date')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('e.g. Jan 2023'),

                TextInput::make('end_date')
                    ->default('Present')
                    ->maxLength(50)
                    ->placeholder('e.g. Present, Dec 2024'),

                TagsInput::make('tech')
                    ->placeholder('Add technologies used...'),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull()
                    ->placeholder('Key responsibilities and achievements...'),
            ]);
    }
}
