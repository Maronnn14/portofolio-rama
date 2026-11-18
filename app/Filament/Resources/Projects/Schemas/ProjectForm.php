<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Project Title'),

                Select::make('category')
                    ->options([
                        'Frontend' => 'Frontend',
                        'Backend' => 'Backend',
                        'Full Stack' => 'Full Stack',
                        'Mobile' => 'Mobile',
                        'UI/UX' => 'UI/UX',
                    ])
                    ->default('Full Stack')
                    ->required(),

                Select::make('status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                    ])
                    ->default('published')
                    ->required(),

                TextInput::make('short_desc')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Brief overview displayed on cards')
                    ->columnSpanFull(),

                Textarea::make('full_desc')
                    ->rows(5)
                    ->placeholder('Detailed description for project detail page...')
                    ->columnSpanFull(),

                FileUpload::make('thumbnail')
                    ->label('Thumbnail Image')
                    ->disk('public')
                    ->directory('projects')
                    ->image()
                    ->formatStateUsing(function ($state, $record) {
                        $raw = $record ? $record->getRawOriginal('thumbnail') : null;
                        if (! $raw || str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, 'data:')) {
                            return null;
                        }

                        return $raw;
                    })
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText(fn ($record) => $record && (str_starts_with($record->getRawOriginal('thumbnail') ?? '', 'http') || str_starts_with($record->getRawOriginal('thumbnail') ?? '', 'data:'))
                        ? 'Current thumbnail is external: '.substr($record->getRawOriginal('thumbnail'), 0, 50).'... Uploading a new file will replace it.'
                        : 'Upload thumbnail image to storage.'
                    ),

                TextInput::make('external_thumbnail')
                    ->label('Or External Thumbnail URL')
                    ->placeholder('https://...')
                    ->dehydrated(false)
                    ->afterStateHydrated(function (TextInput $component, $record) {
                        $raw = $record ? $record->getRawOriginal('thumbnail') : null;
                        if ($raw && (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://'))) {
                            $component->state($raw);
                        }
                    })
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (filled($state)) {
                            $set('thumbnail', $state);
                        }
                    }),

                TagsInput::make('tech')
                    ->placeholder('Add technologies (e.g. Laravel, React, Tailwind)...')
                    ->columnSpanFull(),

                TextInput::make('live_url')
                    ->label('Live Demo URL')
                    ->url()
                    ->maxLength(500)
                    ->placeholder('https://example.com'),

                TextInput::make('source_url')
                    ->label('Source Code URL')
                    ->url()
                    ->maxLength(500)
                    ->placeholder('https://github.com/...'),

                TagsInput::make('gallery')
                    ->label('Gallery Images (URLs or paths)')
                    ->placeholder('Add image URL and hit Enter...')
                    ->columnSpanFull(),

                Toggle::make('featured')
                    ->label('Featured Project')
                    ->default(false),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
