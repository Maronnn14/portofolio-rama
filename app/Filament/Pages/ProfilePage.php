<?php

namespace App\Filament\Pages;

use App\Models\PersonalInfo;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ProfilePage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $slug = 'profile';

    protected static ?int $navigationSort = 9;

    protected string $view = 'filament.pages.profile-page';

    public ?array $data = [];

    public function mount(): void
    {
        $info = PersonalInfo::pluck('value', 'key')->all();
        $this->form->fill($info);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Personal Details')
                    ->description('Public information displayed in header, hero, and contact')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('First Name')
                                ->required(),
                            TextInput::make('nickname')
                                ->label('Nickname'),
                            TextInput::make('fullName')
                                ->label('Full Name')
                                ->required(),
                            TextInput::make('role')
                                ->label('Headline / Role')
                                ->required(),
                            TextInput::make('location')
                                ->label('Location'),
                            TextInput::make('email')
                                ->label('Contact Email')
                                ->email(),
                            TextInput::make('github')
                                ->label('GitHub Username'),
                        ]),
                        TextInput::make('tagline')
                            ->label('Hero Tagline')
                            ->columnSpanFull(),
                        FileUpload::make('profileImage')
                            ->label('Profile Image')
                            ->disk('public')
                            ->directory('profile')
                            ->image()
                            ->formatStateUsing(function ($state) {
                                if (! $state || str_starts_with($state, 'http') || str_starts_with($state, 'data:')) {
                                    return null;
                                }

                                return $state;
                            })
                            ->dehydrated(fn ($state) => filled($state)),
                        TextInput::make('profileImage_url')
                            ->label('Or External Profile Image URL')
                            ->placeholder('https://...')
                            ->afterStateHydrated(function (TextInput $component, $state) {
                                $current = PersonalInfo::where('key', 'profileImage')->value('value');
                                if ($current && (str_starts_with($current, 'http://') || str_starts_with($current, 'https://'))) {
                                    $component->state($current);
                                }
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (filled($state)) {
                                    $set('profileImage', $state);
                                }
                            })
                            ->dehydrated(false),
                        Textarea::make('shortBio')
                            ->label('Short Bio')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('fullBio')
                            ->label('Full Bio (About Page)')
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Section::make('About Quote & Stats')
                    ->description('Featured quote and stat counters on About page')
                    ->schema([
                        Textarea::make('about_quote')
                            ->label('About Quote')
                            ->rows(3)
                            ->columnSpanFull(),
                        Grid::make(3)->schema([
                            TextInput::make('stat_1_value')->label('Stat 1 Value (e.g. 3+)'),
                            TextInput::make('stat_1_label')->label('Stat 1 Label (e.g. Years Experience)'),
                            TextInput::make('stat_2_value')->label('Stat 2 Value (e.g. 15+)'),
                            TextInput::make('stat_2_label')->label('Stat 2 Label (e.g. Projects Delivered)'),
                            TextInput::make('stat_3_value')->label('Stat 3 Value (e.g. 10+)'),
                            TextInput::make('stat_3_label')->label('Stat 3 Label (e.g. Happy Clients)'),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            if ($value !== null) {
                PersonalInfo::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();
    }
}
