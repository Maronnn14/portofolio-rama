<?php

namespace App\Filament\Resources\Skills\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SkillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Skill Name (e.g. Laravel, React, Docker)'),

                Select::make('category')
                    ->options([
                        'Frontend' => 'Frontend',
                        'Backend' => 'Backend',
                        'Tools & DevOps' => 'Tools & DevOps',
                        'Database' => 'Database',
                        'Mobile' => 'Mobile',
                    ])
                    ->default('Frontend')
                    ->required(),

                TextInput::make('icon')
                    ->maxLength(255)
                    ->placeholder('Icon name or class (e.g. devicon-laravel-plain)'),

                TextInput::make('proficiency')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(75)
                    ->suffix('%')
                    ->required(),

                Select::make('level')
                    ->options([
                        'Beginner' => 'Beginner',
                        'Intermediate' => 'Intermediate',
                        'Advanced' => 'Advanced',
                        'Expert' => 'Expert',
                    ])
                    ->default('Intermediate')
                    ->required(),

                TextInput::make('teaser')
                    ->maxLength(255)
                    ->placeholder('One-liner teaser about experience with this skill')
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->rows(4)
                    ->placeholder('Detailed experience, projects, or background...')
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
