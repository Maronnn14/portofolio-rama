<?php

namespace App\Filament\Resources\PortfolioMessages;

use App\Filament\Resources\PortfolioMessages\Pages\CreatePortfolioMessage;
use App\Filament\Resources\PortfolioMessages\Pages\EditPortfolioMessage;
use App\Filament\Resources\PortfolioMessages\Pages\ListPortfolioMessages;
use App\Filament\Resources\PortfolioMessages\Schemas\PortfolioMessageForm;
use App\Filament\Resources\PortfolioMessages\Tables\PortfolioMessagesTable;
use App\Models\PortfolioMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PortfolioMessageResource extends Resource
{
    protected static ?string $model = PortfolioMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|\UnitEnum|null $navigationGroup = 'Home';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Messages';

    public static function form(Schema $schema): Schema
    {
        return PortfolioMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortfolioMessagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPortfolioMessages::route('/'),
            'create' => CreatePortfolioMessage::route('/create'),
            'edit' => EditPortfolioMessage::route('/{record}/edit'),
        ];
    }
}
