<?php

namespace App\Filament\Resources\SoftSkills\Pages;

use App\Filament\Resources\SoftSkills\SoftSkillResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSoftSkills extends ListRecords
{
    protected static string $resource = SoftSkillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
