<?php

namespace App\Filament\Pages;

use App\Models\ContactSubmission;
use App\Models\Experience;
use App\Models\GalleryImage;
use App\Models\Interest;
use App\Models\PersonalInfo;
use App\Models\PortfolioMessage;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\Social;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingsPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $slug = 'settings';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.pages.settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::getSetting('general', [
            'site_title' => 'Rama Adin - Full Stack Developer',
            'meta_description' => 'Full Stack Developer crafting digital experiences with code and creativity.',
            'enable_contact_form' => true,
            'enable_guestbook' => true,
        ]);

        $this->form->fill($settings ?: [
            'site_title' => 'Rama Adin - Full Stack Developer',
            'meta_description' => 'Full Stack Developer crafting digital experiences with code and creativity.',
            'enable_contact_form' => true,
            'enable_guestbook' => true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('General & SEO Settings')
                    ->description('Configuration for site title, meta description, and public features')
                    ->schema([
                        TextInput::make('site_title')
                            ->label('Site Title')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
                            Toggle::make('enable_contact_form')
                                ->label('Enable Contact Form')
                                ->default(true),

                            Toggle::make('enable_guestbook')
                                ->label('Enable Guestbook / Messages')
                                ->default(true),
                        ]),
                    ]),

                Section::make('Security & Password')
                    ->description('Update your admin account login password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false),

                        Grid::make(2)->schema([
                            TextInput::make('new_password')
                                ->label('New Password')
                                ->password()
                                ->revealable()
                                ->minLength(8)
                                ->dehydrated(false),

                            TextInput::make('new_password_confirmation')
                                ->label('Confirm New Password')
                                ->password()
                                ->revealable()
                                ->dehydrated(false),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        // Handle password change if filled
        if (! empty($this->data['new_password'])) {
            $user = Filament::auth()->user();

            if (! Hash::check($this->data['current_password'] ?? '', $user->password)) {
                Notification::make()
                    ->title('Current password does not match')
                    ->danger()
                    ->send();

                return;
            }

            if ($this->data['new_password'] !== ($this->data['new_password_confirmation'] ?? '')) {
                Notification::make()
                    ->title('New password confirmation does not match')
                    ->danger()
                    ->send();

                return;
            }

            $user->update([
                'password' => Hash::make($this->data['new_password']),
            ]);

            $this->data['current_password'] = null;
            $this->data['new_password'] = null;
            $this->data['new_password_confirmation'] = null;

            Notification::make()
                ->title('Password updated successfully')
                ->success()
                ->send();
        }

        // Save general settings
        $general = [
            'site_title' => $state['site_title'] ?? 'Rama Adin - Full Stack Developer',
            'meta_description' => $state['meta_description'] ?? '',
            'enable_contact_form' => (bool) ($state['enable_contact_form'] ?? true),
            'enable_guestbook' => (bool) ($state['enable_guestbook'] ?? true),
        ];

        SiteSetting::updateOrCreate(
            ['key' => 'general'],
            ['value' => $general]
        );

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    public function exportData(): StreamedResponse
    {
        $backup = [
            'exported_at' => now()->toIso8601String(),
            'personal_info' => PersonalInfo::all()->toArray(),
            'site_settings' => SiteSetting::all()->toArray(),
            'projects' => Project::all()->toArray(),
            'skills' => Skill::with(['projectLinks', 'galleryItems'])->get()->toArray(),
            'experiences' => Experience::all()->toArray(),
            'socials' => Social::all()->toArray(),
            'interests' => Interest::all()->toArray(),
            'gallery_images' => GalleryImage::all()->toArray(),
            'portfolio_messages' => PortfolioMessage::all()->toArray(),
            'contact_submissions' => ContactSubmission::all()->toArray(),
        ];

        $json = json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, 'portfolio-backup-'.date('Y-m-d-His').'.json', [
            'Content-Type' => 'application/json',
        ]);
    }
}
