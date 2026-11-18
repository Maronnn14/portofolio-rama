<?php

namespace App\Filament\Resources\Skills\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'projectLinks';

    protected static ?string $title = 'Related Projects & Links';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g. E-Commerce Platform'),

                TextInput::make('url')
                    ->required()
                    ->url()
                    ->maxLength(500)
                    ->placeholder('https://...'),

                TextInput::make('description')
                    ->maxLength(100)
                    ->placeholder('Short description or role'),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('url')
                    ->limit(40)
                    ->url(fn ($record) => $record->url, true),

                TextColumn::make('description')
                    ->limit(40),

                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
