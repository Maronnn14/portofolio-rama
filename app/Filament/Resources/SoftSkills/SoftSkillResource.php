<?php

namespace App\Filament\Resources\SoftSkills;

use App\Filament\Resources\SoftSkills\Pages\CreateSoftSkill;
use App\Filament\Resources\SoftSkills\Pages\EditSoftSkill;
use App\Filament\Resources\SoftSkills\Pages\ListSoftSkills;
use App\Filament\Resources\SoftSkills\Schemas\SoftSkillForm;
use App\Filament\Resources\SoftSkills\Tables\SoftSkillsTable;
use App\Models\SoftSkill;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SoftSkillResource extends Resource
{
    protected static ?string $model = SoftSkill::class;

    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Skills';

    protected static ?string $navigationLabel = 'Soft Skills';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SoftSkillForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SoftSkillsTable::configure($table);
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
            'index' => ListSoftSkills::route('/'),
            'create' => CreateSoftSkill::route('/create'),
            'edit' => EditSoftSkill::route('/{record}/edit'),
        ];
    }
}
