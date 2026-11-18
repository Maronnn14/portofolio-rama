<?php

namespace App\Filament\Resources\SoftSkills\Schemas;

use App\Models\SoftSkill;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SoftSkillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Communication, Teamwork, Problem Solving'),

                TextInput::make('icon')
                    ->maxLength(255)
                    ->placeholder('Emoji (🤝) or image URL')
                    ->helperText('Optional. Shown on the Skills page.'),

                Textarea::make('description')
                    ->rows(4)
                    ->placeholder('Brief description or a concrete example of this skill in action.')
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(fn () => (int) SoftSkill::query()->max('sort_order') + 1)
                    ->required()
                    ->helperText('Lower numbers appear first. Drag rows in the list to reorder.'),
            ]);
    }
}
