<?php

namespace App\Filament\Resources\PortfolioMessages\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PortfolioMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('posted_at_ms', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('rating')
                    ->formatStateUsing(fn ($state) => str_repeat('★', (int) $state).str_repeat('☆', max(0, 5 - (int) $state)))
                    ->sortable(),

                TextColumn::make('message')
                    ->limit(50)
                    ->searchable(),

                ToggleColumn::make('pinned')
                    ->sortable(),

                ToggleColumn::make('flagged')
                    ->sortable(),

                ToggleColumn::make('hidden')
                    ->sortable(),

                TextColumn::make('posted_at_ms')
                    ->label('Posted')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::createFromTimestampMs($state)->diffForHumans() : '-')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('hidden')
                    ->label('Hidden'),
                TernaryFilter::make('flagged')
                    ->label('Flagged'),
                TernaryFilter::make('pinned')
                    ->label('Pinned'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('hide')
                        ->label('Hide Selected')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['hidden' => true]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('unhide')
                        ->label('Unhide Selected')
                        ->icon('heroicon-o-eye')
                        ->action(fn (Collection $records) => $records->each->update(['hidden' => false]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('flag')
                        ->label('Flag Selected')
                        ->icon('heroicon-o-flag')
                        ->action(fn (Collection $records) => $records->each->update(['flagged' => true]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('pin')
                        ->label('Pin Selected')
                        ->icon('heroicon-o-bookmark')
                        ->action(fn (Collection $records) => $records->each->update(['pinned' => true]))
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
