<?php

namespace App\Filament\Resources\Educations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EducationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('program')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('institution')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_year')
                    ->label('From')
                    ->sortable(),

                TextColumn::make('end_year')
                    ->label('To')
                    ->placeholder('Present'),

                TextColumn::make('location')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
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
