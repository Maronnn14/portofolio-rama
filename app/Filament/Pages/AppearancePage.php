<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AppearancePage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static ?string $navigationLabel = 'Appearance';

    protected static ?string $slug = 'appearance';

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.appearance-page';

    public ?array $data = [];

    public function mount(): void
    {
        $appearance = SiteSetting::getSetting('appearance', [
            'accentColor' => '#10b981',
            'theme' => 'dark',
        ]);

        $this->form->fill($appearance ?: [
            'accentColor' => '#10b981',
            'theme' => 'dark',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Theme & Branding')
                    ->description('Customize the visual presentation of your public portfolio')
                    ->schema([
                        ColorPicker::make('accentColor')
                            ->label('Primary Accent Color')
                            ->default('#10b981')
                            ->required(),

                        Select::make('theme')
                            ->label('Base Theme')
                            ->options([
                                'dark' => 'Dark Mode (Recommended)',
                                'light' => 'Light Mode',
                                'system' => 'System Preference',
                            ])
                            ->default('dark')
                            ->required(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        SiteSetting::updateOrCreate(
            ['key' => 'appearance'],
            ['value' => $state]
        );

        Notification::make()
            ->title('Appearance settings saved')
            ->success()
            ->send();
    }
}
