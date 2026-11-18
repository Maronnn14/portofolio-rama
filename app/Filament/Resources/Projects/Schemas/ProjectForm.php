<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Project Title'),

                            TextInput::make('slug')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('Leave blank to generate from the project name.'),

                            TextInput::make('category')
                                ->required()
                                ->maxLength(100)
                                ->datalist([
                                    'Frontend',
                                    'Backend',
                                    'Full Stack',
                                    'Mobile',
                                    'UI/UX',
                                    'Design',
                                ])
                                ->default('Full Stack')
                                ->placeholder('e.g. Full Stack'),

                            TextInput::make('sort_order')
                                ->numeric()
                                ->default(fn () => (int) Project::query()->max('sort_order') + 1)
                                ->required(),
                        ]),
                    ]),

                Section::make('Summary')
                    ->schema([
                        TextInput::make('short_desc')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Brief overview displayed on project cards')
                            ->columnSpanFull(),

                        Textarea::make('full_desc')
                            ->rows(4)
                            ->placeholder('Detailed description used when no article body is provided...')
                            ->columnSpanFull(),
                    ]),

                Section::make('Article Body')
                    ->description('Rendered as a blog-style article on the project detail page. Markdown is supported.')
                    ->schema([
                        MarkdownEditor::make('body')
                            ->label('Case Study / Article')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('projects/article')
                            ->columnSpanFull(),
                    ]),

                Section::make('Media')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->label('Thumbnail Image')
                            ->disk('public')
                            ->directory('projects')
                            ->image()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->formatStateUsing(function ($state, $record) {
                                $raw = $record ? $record->getRawOriginal('thumbnail') : null;
                                if (! $raw || str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, 'data:')) {
                                    return null;
                                }

                                return $raw;
                            })
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText(fn ($record) => $record && (str_starts_with($record->getRawOriginal('thumbnail') ?? '', 'http') || str_starts_with($record->getRawOriginal('thumbnail') ?? '', 'data:'))
                                ? 'Current thumbnail is external. Upload a new file to replace it.'
                                : 'Upload a cover image to storage.'
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
                    ]),

                Section::make('Tech & Links')
                    ->schema([
                        TagsInput::make('tech')
                            ->placeholder('Add technologies (e.g. Laravel, React, Tailwind)...')
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
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
                        ]),
                    ]),

                Section::make('Publishing')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('status')
                                ->options([
                                    'published' => 'Published',
                                    'draft' => 'Draft',
                                    'archived' => 'Archived',
                                ])
                                ->default('published')
                                ->required(),

                            DateTimePicker::make('published_at')
                                ->label('Publish Date')
                                ->helperText('Leave empty to publish immediately.')
                                ->seconds(false),

                            Toggle::make('featured')
                                ->label('Featured on Home')
                                ->default(false)
                                ->helperText('Featured projects appear on the home page.'),
                        ]),
                    ]),

                Section::make('Legacy Gallery')
                    ->description('Optional list of image URLs. Prefer the Project Gallery tab below for uploaded screenshots.')
                    ->collapsed()
                    ->schema([
                        TagsInput::make('gallery')
                            ->label('Gallery Image URLs')
                            ->placeholder('Add image URL and hit Enter...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
