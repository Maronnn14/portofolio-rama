<?php

namespace App\Filament\Resources\Educations\Schemas;

use App\Models\Education;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EducationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Institution & Program')
                    ->description('Education entries are shown as a timeline on the Biodata page.')
                    ->schema([
                        TextInput::make('institution')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. University of Technology'),

                        TextInput::make('program')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Bachelor of Computer Science'),

                        Grid::make(3)->schema([
                            TextInput::make('start_year')
                                ->required()
                                ->maxLength(20)
                                ->placeholder('e.g. 2020'),

                            TextInput::make('end_year')
                                ->maxLength(20)
                                ->placeholder('e.g. 2024 or Present'),

                            TextInput::make('location')
                                ->maxLength(255)
                                ->placeholder('e.g. Jakarta, Indonesia'),
                        ]),
                    ]),

                Section::make('Highlights')
                    ->schema([
                        Textarea::make('description')
                            ->rows(4)
                            ->placeholder('What did you study or accomplish?')
                            ->columnSpanFull(),

                        TagsInput::make('highlights')
                            ->placeholder('Add a highlight and press Enter (e.g. GPA 3.8)')
                            ->helperText('Each entry becomes a bullet point under the timeline item.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Ordering')
                    ->schema([
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(fn () => (int) Education::query()->max('sort_order') + 1)
                            ->required()
                            ->helperText('Lower numbers appear first. You can also drag rows in the list.'),
                    ]),
            ]);
    }
}
