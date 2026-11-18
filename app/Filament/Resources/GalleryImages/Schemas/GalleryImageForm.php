<?php

namespace App\Filament\Resources\GalleryImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GalleryImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('url')
                    ->label('Image File')
                    ->disk('public')
                    ->directory('gallery')
                    ->image()
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                    ->formatStateUsing(function ($state, $record) {
                        $raw = $record ? $record->getRawOriginal('url') : null;
                        if (! $raw || str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, 'data:')) {
                            return null;
                        }

                        return $raw;
                    })
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText(fn ($record) => $record && (str_starts_with($record->getRawOriginal('url') ?? '', 'http') || str_starts_with($record->getRawOriginal('url') ?? '', 'data:'))
                        ? 'Current image source: '.substr($record->getRawOriginal('url'), 0, 50).'... Uploading a new file will replace it.'
                        : 'Upload image file to public gallery storage.'
                    ),

                TextInput::make('external_url')
                    ->label('Or External Image URL')
                    ->placeholder('https://...')
                    ->dehydrated(false)
                    ->afterStateHydrated(function (TextInput $component, $record) {
                        $raw = $record ? $record->getRawOriginal('url') : null;
                        if ($raw && (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://'))) {
                            $component->state($raw);
                        }
                    })
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (filled($state)) {
                            $set('url', $state);
                        }
                    }),

                TextInput::make('alt')
                    ->maxLength(255)
                    ->placeholder('Alternative text for accessibility'),

                TextInput::make('category')
                    ->maxLength(100)
                    ->placeholder('e.g. Photography, Design, Setup, Certificate'),

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
