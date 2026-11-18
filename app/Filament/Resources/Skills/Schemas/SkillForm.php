<?php

namespace App\Filament\Resources\Skills\Schemas;

use App\Models\Skill;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SkillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Skill Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Skill Name (e.g. Laravel, React, Docker)'),

                        Select::make('skill_type')
                            ->label('Skill Type')
                            ->options([
                                'tech' => 'Tech Stack (coding skill)',
                                'other' => 'Other Hard Skill (non-coding)',
                            ])
                            ->default('tech')
                            ->required()
                            ->live()
                            ->helperText('Tech stack skills get category filters. Other hard skills (Excel, Design, Office…) appear in the “Beyond the Code” section.'),

                        TextInput::make('category')
                            ->required()
                            ->maxLength(100)
                            ->datalist(fn () => Skill::query()
                                ->select('category')
                                ->distinct()
                                ->orderBy('category')
                                ->pluck('category')
                                ->filter()
                                ->values()
                                ->all())
                            ->default('Frontend')
                            ->placeholder('e.g. Frontend, Backend, Tools — or Spreadsheet, Design, Office for non-coding skills'),

                        TextInput::make('icon')
                            ->maxLength(255)
                            ->placeholder('Icon name or image URL (e.g. laravel, react, docker)')
                            ->helperText('Known Devicon names and image URLs are supported.'),

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

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(fn () => (int) Skill::query()->max('sort_order') + 1)
                            ->required(),
                    ]),

                Section::make('Public Content')
                    ->schema([
                        TextInput::make('teaser')
                            ->maxLength(255)
                            ->placeholder('One-liner teaser about experience with this skill')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->rows(4)
                            ->placeholder('Detailed experience, projects, or background...')
                            ->columnSpanFull(),

                        Toggle::make('featured')
                            ->label('Highlight on Home Page')
                            ->helperText('Featured skills appear in the home page tech stack.')
                            ->default(false),
                    ]),
            ]);
    }
}
