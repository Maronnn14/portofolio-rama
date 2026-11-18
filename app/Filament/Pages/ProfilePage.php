<?php

namespace App\Filament\Pages;

use App\Models\PersonalInfo;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class ProfilePage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static string|\UnitEnum|null $navigationGroup = 'About Me';

    protected static ?string $navigationLabel = 'Profile (Biodata & Branding)';

    protected static ?string $slug = 'profile';

    protected static ?int $navigationSort = 1;

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
                    ->description('Public information displayed in the header, hero, biodata, and contact pages.')
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
                        Textarea::make('shortBio')
                            ->label('Short Bio')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('fullBio')
                            ->label('Full Bio (Biodata page)')
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Section::make('Personal Branding')
                    ->description('Values, strengths, and what makes you unique — shown on the Biodata page.')
                    ->schema([
                        Textarea::make('branding_statement')
                            ->label('Personal Branding Statement')
                            ->rows(4)
                            ->placeholder('I build reliable, human-centred digital products...')
                            ->columnSpanFull(),
                        Textarea::make('interests_intro')
                            ->label('Interests Page Intro')
                            ->rows(2)
                            ->placeholder('Optional introduction for the Interests & Hobbies page.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Photos')
                    ->description('Upload images or leave empty to keep the current photo.')
                    ->schema([
                        FileUpload::make('profileImage')
                            ->label('Profile Photo')
                            ->disk('public')
                            ->directory('profile')
                            ->image()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->formatStateUsing(fn ($state) => $this->fileState($state))
                            ->dehydrated(fn ($state) => filled($state)),

                        FileUpload::make('homeProfileImage')
                            ->label('Home Page Photo (optional)')
                            ->disk('public')
                            ->directory('profile')
                            ->image()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->formatStateUsing(fn ($state) => $this->fileState($state))
                            ->dehydrated(fn ($state) => filled($state)),

                        FileUpload::make('aboutProfileImage')
                            ->label('Biodata Page Photo (optional)')
                            ->disk('public')
                            ->directory('profile')
                            ->image()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->formatStateUsing(fn ($state) => $this->fileState($state))
                            ->dehydrated(fn ($state) => filled($state)),
                    ]),

                Section::make('CV / Resume')
                    ->description('Upload the CV PDF and keep its label/version up to date. Shown on the Download CV page.')
                    ->schema([
                        FileUpload::make('cv_file')
                            ->label('CV File (PDF)')
                            ->disk('public')
                            ->directory('cv')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->formatStateUsing(fn ($state) => $this->fileState($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText(function () {
                                $current = PersonalInfo::cvUrl();

                                return $current
                                    ? 'Current file: '.$current.' — upload a new PDF to replace it.'
                                    : 'No CV uploaded yet.';
                            }),

                        Grid::make(3)->schema([
                            TextInput::make('cv_label')
                                ->label('CV Label')
                                ->default('Curriculum Vitae'),

                            TextInput::make('cv_version')
                                ->label('Version'),

                            DatePicker::make('cv_updated_at')
                                ->label('Last Updated'),
                        ]),
                    ]),

                Section::make('About Quote & Stats')
                    ->description('Featured quote and stat counters on the About page.')
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

    private function fileState(mixed $state): ?string
    {
        if (! $state || str_starts_with((string) $state, 'http') || str_starts_with((string) $state, 'data:')) {
            return null;
        }

        return (string) $state;
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $previousCv = PersonalInfo::query()->where('key', 'cv_file')->value('value');

        foreach ($state as $key => $value) {
            if ($value !== null) {
                PersonalInfo::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        // Remove the replaced CV so old PDFs do not pile up in storage.
        $newCv = $state['cv_file'] ?? null;

        if (filled($previousCv) && filled($newCv) && $previousCv !== $newCv && ! str_starts_with((string) $previousCv, 'http')) {
            Storage::disk('public')->delete($previousCv);
        }

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();
    }
}
