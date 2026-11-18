<?php

namespace App\Filament\Resources\Skills\Tables;

use App\Models\Skill;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SkillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('skill_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'other' ? 'Other skill' : 'Tech stack')
                    ->color(fn (string $state): string => $state === 'other' ? 'warning' : 'success')
                    ->sortable(),

                TextColumn::make('category')
                    ->badge()
                    ->sortable(),

                TextColumn::make('level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Expert' => 'success',
                        'Advanced' => 'info',
                        'Intermediate' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('proficiency')
                    ->formatStateUsing(fn ($state) => $state.'%')
                    ->sortable(),

                ToggleColumn::make('featured')
                    ->label('Home')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('skill_type')
                    ->label('Skill type')
                    ->options([
                        'tech' => 'Tech stack',
                        'other' => 'Other hard skill',
                    ]),

                SelectFilter::make('category')
                    ->options(fn () => Skill::query()
                        ->select('category')
                        ->distinct()
                        ->orderBy('category')
                        ->pluck('category', 'category')
                        ->all()),
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
