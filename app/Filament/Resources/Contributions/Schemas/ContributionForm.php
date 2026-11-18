<?php

namespace App\Filament\Resources\Contributions\Schemas;

use App\Models\Contribution;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContributionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contribution Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Community, organization, or project name'),

                            TextInput::make('slug')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('Leave blank to generate from the name.'),

                            TextInput::make('role')
                                ->maxLength(255)
                                ->placeholder('e.g. Core Team Member, Volunteer, Speaker'),

                            TextInput::make('organization')
                                ->maxLength(255)
                                ->placeholder('Parent organization (optional)'),

                            TextInput::make('period_start')
                                ->maxLength(20)
                                ->placeholder('e.g. 2023'),

                            TextInput::make('period_end')
                                ->maxLength(20)
                                ->placeholder('e.g. 2024 or Present'),
                        ]),
                    ]),

                Section::make('Summary')
                    ->schema([
                        Textarea::make('description')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('A short summary shown on the listing page.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Article Body')
                    ->description('Rendered as a blog-style article. Markdown is supported.')
                    ->schema([
                        MarkdownEditor::make('body')
                            ->label('Contribution Story')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('contributions')
                            ->columnSpanFull(),
                    ]),

                Section::make('Media')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Cover Image')
                            ->disk('public')
                            ->directory('contributions')
                            ->image()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->formatStateUsing(function ($state) {
                                if (! $state || str_starts_with($state, 'http') || str_starts_with($state, 'data:')) {
                                    return null;
                                }

                                return $state;
                            })
                            ->helperText('Upload a cover image. Leave empty to keep the current image.')
                            ->dehydrated(fn ($state) => filled($state)),

                        TextInput::make('external_cover_url')
                            ->label('Or External Cover URL')
                            ->url()
                            ->placeholder('https://...')
                            ->dehydrated(false)
                            ->afterStateHydrated(function (TextInput $component, $record) {
                                $raw = $record?->getRawOriginal('cover_image');
                                if ($raw && str_starts_with($raw, 'http')) {
                                    $component->state($raw);
                                }
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (filled($state)) {
                                    $set('cover_image', $state);
                                }
                            }),

                        FileUpload::make('images')
                            ->label('Gallery Images')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->disk('public')
                            ->directory('contributions')
                            ->columnSpanFull(),
                    ]),

                Section::make('Links')
                    ->description('Related profiles, pull requests, certificates, or articles.')
                    ->schema([
                        Repeater::make('links')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('e.g. Certificate'),
                                TextInput::make('url')
                                    ->required()
                                    ->url()
                                    ->maxLength(500)
                                    ->placeholder('https://...'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

                Section::make('Publishing')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('status')
                                ->options([
                                    'published' => 'Published',
                                    'draft' => 'Draft',
                                ])
                                ->default('published')
                                ->required(),

                            DateTimePicker::make('published_at')
                                ->label('Publish Date')
                                ->helperText('Leave empty to publish immediately.')
                                ->seconds(false),

                            TextInput::make('sort_order')
                                ->numeric()
                                ->default(fn () => (int) Contribution::query()->max('sort_order') + 1)
                                ->required(),
                        ]),
                    ]),
            ]);
    }
}
