<?php

namespace App\Filament\Resources\PortfolioMessages\Pages;

use App\Filament\Resources\PortfolioMessages\PortfolioMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPortfolioMessage extends EditRecord
{
    protected static string $resource = PortfolioMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
