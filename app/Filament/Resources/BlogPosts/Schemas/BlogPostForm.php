<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

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

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tulisan')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->label('Judul')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Mis. Belajar Laravel: catatan hari ke-12'),

                            TextInput::make('slug')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('Kosongkan untuk dibuat otomatis dari judul.'),

                            TextInput::make('category')
                                ->label('Kategori')
                                ->maxLength(100)
                                ->placeholder('Mis. Daily Note, Devlog, Tutorial')
                                ->datalist(['Daily Note', 'Devlog', 'Tutorial', 'Reflection']),

                            TagsInput::make('tags')
                                ->label('Tag')
                                ->placeholder('Tambah tag lalu Enter'),
                        ]),

                        Textarea::make('excerpt')
                            ->label('Ringkasan singkat')
                            ->rows(3)
                            ->maxLength(400)
                            ->helperText('Tampil di kartu daftar blog dan beranda. Kosongkan untuk memotong otomatis dari isi.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Isi Tulisan')
                    ->description('Mendukung Markdown: ## judul, **tebal**, daftar, kutipan, dan tautan.')
                    ->schema([
                        MarkdownEditor::make('body')
                            ->label('Isi')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('blog')
                            ->columnSpanFull(),
                    ]),

                Section::make('Gambar Sampul')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Cover')
                            ->disk('public')
                            ->directory('blog')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->formatStateUsing(function ($state) {
                                if (! $state || str_starts_with($state, 'http') || str_starts_with($state, 'data:')) {
                                    return null;
                                }

                                return $state;
                            })
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText('Rasio 16:9 paling pas untuk kartu blog. Kosongkan untuk memakai kotak inisial judul.'),

                        TextInput::make('external_cover_url')
                            ->label('Atau URL cover eksternal')
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

                Section::make('Publikasi')
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
                                ->label('Tanggal terbit')
                                ->helperText('Kosongkan agar dianggap terbit sekarang.')
                                ->seconds(false)
                                ->default(fn () => now()),
                        ]),
                    ]),
            ]);
    }
}
