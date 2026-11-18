<?php

namespace App\Filament\Resources\Experiences\Schemas;

use App\Models\Experience;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('role')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g. Full Stack Developer'),

                            TextInput::make('slug')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('Leave blank to generate from the role.'),

                            TextInput::make('company')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Company or Organization name'),

                            TextInput::make('location')
                                ->maxLength(255)
                                ->placeholder('e.g. Jakarta, Indonesia or Remote'),

                            TextInput::make('type')
                                ->required()
                                ->maxLength(50)
                                ->datalist([
                                    'Full-time',
                                    'Part-time',
                                    'Contract',
                                    'Internship',
                                    'Freelance',
                                    'Organization',
                                    'Education',
                                ])
                                ->default('Full-time')
                                ->placeholder('e.g. Full-time'),

                            TextInput::make('sort_order')
                                ->numeric()
                                ->default(fn () => (int) Experience::query()->max('sort_order') + 1)
                                ->required(),

                            TextInput::make('start_date')
                                ->required()
                                ->maxLength(50)
                                ->placeholder('e.g. Jan 2023'),

                            TextInput::make('end_date')
                                ->maxLength(50)
                                ->placeholder('e.g. Present, Dec 2024'),
                        ]),
                    ]),

                Section::make('Summary')
                    ->schema([
                        Textarea::make('description')
                            ->rows(4)
                            ->placeholder('Key responsibilities and measurable outcomes...')
                            ->columnSpanFull(),
                    ]),

                Section::make('Article Body')
                    ->description('Rendered as a blog-style article on the experience detail page. Markdown is supported.')
                    ->schema([
                        MarkdownEditor::make('body')
                            ->label('Experience Story')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('experiences')
                            ->columnSpanFull(),
                    ]),

                Section::make('Achievements & Tech')
                    ->schema([
                        TagsInput::make('achievements')
                            ->placeholder('Add an achievement and press Enter (e.g. Reduced load time by 40%)')
                            ->helperText('Shown as bullet points on the detail page and CV.')
                            ->columnSpanFull(),

                        TagsInput::make('tech')
                            ->placeholder('Add technologies used...')
                            ->columnSpanFull(),
                    ]),

                Section::make('Media')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Cover Image')
                            ->disk('public')
                            ->directory('experiences')
                            ->image()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->formatStateUsing(function ($state) {
                                if (! $state || str_starts_with($state, 'http') || str_starts_with($state, 'data:')) {
                                    return null;
                                }

                                return $state;
                            })
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText('Upload a cover image. Leave empty to keep the current image.'),

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
                    ]),

                Section::make('Publishing')
                    ->schema([
                        Grid::make(2)->schema([
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
                        ]),
                    ]),
            ]);
    }
}
