<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class HomePage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static string|\UnitEnum|null $navigationGroup = 'Home';

    protected static ?string $navigationLabel = 'Home Page';

    protected static ?string $slug = 'home';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Home Page Content';

    protected string $view = 'filament.pages.home-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::home());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Hero Call-to-Action')
                    ->description('The name, role, tagline, and hero photo are managed in Profile → Personal Details.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('hero_cta_primary_label')
                                ->label('Primary Button Label')
                                ->default('View Projects'),

                            TextInput::make('hero_cta_primary_url')
                                ->label('Primary Button URL')
                                ->default('/projects')
                                ->helperText('Relative URL or full https:// link.'),

                            TextInput::make('hero_cta_secondary_label')
                                ->label('Secondary Button Label')
                                ->default('Contact Me'),

                            TextInput::make('hero_cta_secondary_url')
                                ->label('Secondary Button URL')
                                ->default('/contact'),
                        ]),
                    ]),

                Section::make('About Teaser')
                    ->description('Short introduction shown in the home page "About Me" preview.')
                    ->schema([
                        Textarea::make('about_teaser')
                            ->rows(4)
                            ->placeholder('Leave empty to use the short bio from your profile.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Contact Snippet')
                    ->schema([
                        TextInput::make('contact_cta_heading')
                            ->label('Heading')
                            ->default("Let's Work Together"),

                        TextInput::make('contact_cta_text')
                            ->label('Supporting Text')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        SiteSetting::updateOrCreate(
            ['key' => 'home'],
            ['value' => [
                'hero_cta_primary_label' => $state['hero_cta_primary_label'] ?? 'View Projects',
                'hero_cta_primary_url' => $state['hero_cta_primary_url'] ?? '/projects',
                'hero_cta_secondary_label' => $state['hero_cta_secondary_label'] ?? 'Contact Me',
                'hero_cta_secondary_url' => $state['hero_cta_secondary_url'] ?? '/contact',
                'about_teaser' => $state['about_teaser'] ?? null,
                'contact_cta_heading' => $state['contact_cta_heading'] ?? "Let's Work Together",
                'contact_cta_text' => $state['contact_cta_text'] ?? null,
            ]],
        );

        Notification::make()
            ->title('Home page content saved')
            ->success()
            ->send();
    }
}
