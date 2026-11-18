<?php

namespace App\Filament\Pages;

use App\Models\ContactSubmission;
use App\Models\Contribution;
use App\Models\Education;
use App\Models\Experience;
use App\Models\GalleryImage;
use App\Models\Interest;
use App\Models\PersonalInfo;
use App\Models\PortfolioMessage;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\Social;
use App\Models\SoftSkill;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $slug = 'settings';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $general = SiteSetting::general();
        $home = SiteSetting::home();
        $navigation = SiteSetting::navigation();

        $navItems = collect($navigation['order'])
            ->map(fn (string $key): array => [
                'key' => $key,
                'label' => $navigation['labels'][$key] ?? ucfirst($key),
            ])
            ->values()
            ->all();

        $this->form->fill(array_merge($general, $home, [
            'nav_items' => $navItems,
            'nav_about_biodata' => $navigation['labels']['about_biodata'] ?? 'Biodata & Personal Branding',
            'nav_about_cv' => $navigation['labels']['about_cv'] ?? 'Download CV',
            'nav_about_interests' => $navigation['labels']['about_interests'] ?? 'Interests & Hobbies',
            'nav_projects_coding' => $navigation['labels']['projects_coding'] ?? 'Coding Projects',
            'nav_projects_experience' => $navigation['labels']['projects_experience'] ?? 'Work Experience',
            'nav_projects_contributions' => $navigation['labels']['projects_contributions'] ?? 'Contributions & Activities',
        ]));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('General & SEO')
                    ->description('Site title, search metadata, and the social sharing image.')
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

                        FileUpload::make('og_image')
                            ->label('Open Graph Image')
                            ->disk('public')
                            ->directory('site')
                            ->image()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->formatStateUsing(fn ($state) => $state && ! str_starts_with((string) $state, 'http') && ! str_starts_with((string) $state, 'data:') ? $state : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText('Recommended 1200×630px. Leave empty to keep the current image.'),

                        Grid::make(2)->schema([
                            TextInput::make('contact_email')
                                ->label('Public Contact Email')
                                ->email()
                                ->helperText('Falls back to the email in Profile.'),

                            TextInput::make('availability')
                                ->label('Availability Badge')
                                ->placeholder('e.g. Open to opportunities'),
                        ]),
                    ]),

                Section::make('Footer')
                    ->schema([
                        TextInput::make('footer_text')
                            ->label('Footer Tagline')
                            ->placeholder('Built with passion & code'),

                        TextInput::make('copyright_text')
                            ->label('Copyright Text')
                            ->helperText('Leave empty to auto-generate.'),
                    ]),

                Section::make('Navigation')
                    ->description('Rename menu items and drag them to change the order. Dropdown labels are below.')
                    ->schema([
                        Repeater::make('nav_items')
                            ->hiddenLabel()
                            ->schema([
                                Select::make('key')
                                    ->label('Menu item')
                                    ->options([
                                        'home' => 'Home',
                                        'about' => 'About Me (dropdown)',
                                        'skills' => 'Skills',
                                        'projects' => 'Projects & Experience (dropdown)',
                                        'contact' => 'Contact',
                                    ])
                                    ->required()
                                    ->native(false),

                                TextInput::make('label')
                                    ->label('Label')
                                    ->required()
                                    ->maxLength(60),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->addActionLabel('Add menu item')
                            ->columnSpanFull(),

                        Grid::make(3)->schema([
                            TextInput::make('nav_about_biodata')->label('About → Biodata label'),
                            TextInput::make('nav_about_cv')->label('About → CV label'),
                            TextInput::make('nav_about_interests')->label('About → Interests label'),
                            TextInput::make('nav_projects_coding')->label('Projects → Coding label'),
                            TextInput::make('nav_projects_experience')->label('Projects → Experience label'),
                            TextInput::make('nav_projects_contributions')->label('Projects → Contributions label'),
                        ]),
                    ]),

                Section::make('Home Page')
                    ->description('Hero buttons, about teaser, and the contact call-to-action on the home page.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('hero_cta_primary_label')->label('Primary Button Label'),
                            TextInput::make('hero_cta_primary_url')->label('Primary Button URL'),
                            TextInput::make('hero_cta_secondary_label')->label('Secondary Button Label'),
                            TextInput::make('hero_cta_secondary_url')->label('Secondary Button URL'),
                        ]),

                        Textarea::make('about_teaser')
                            ->label('About Teaser')
                            ->rows(3)
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
                            TextInput::make('contact_cta_heading')->label('Contact CTA Heading'),
                            TextInput::make('contact_cta_text')->label('Contact CTA Text'),
                        ]),
                    ]),

                Section::make('Security & Password')
                    ->description('Update your admin account login password.')
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

        SiteSetting::updateOrCreate(['key' => 'general'], ['value' => [
            'site_title' => $state['site_title'] ?? config('app.name'),
            'meta_description' => $state['meta_description'] ?? '',
            'og_image' => $state['og_image'] ?? null,
            'footer_text' => $state['footer_text'] ?? null,
            'copyright_text' => $state['copyright_text'] ?? null,
            'contact_email' => $state['contact_email'] ?? null,
            'availability' => $state['availability'] ?? null,
        ]]);

        SiteSetting::updateOrCreate(['key' => 'home'], ['value' => [
            'hero_cta_primary_label' => $state['hero_cta_primary_label'] ?? 'View Projects',
            'hero_cta_primary_url' => $state['hero_cta_primary_url'] ?? '/projects',
            'hero_cta_secondary_label' => $state['hero_cta_secondary_label'] ?? 'Contact Me',
            'hero_cta_secondary_url' => $state['hero_cta_secondary_url'] ?? '/contact',
            'about_teaser' => $state['about_teaser'] ?? null,
            'contact_cta_heading' => $state['contact_cta_heading'] ?? "Let's Work Together",
            'contact_cta_text' => $state['contact_cta_text'] ?? null,
        ]]);

        $items = collect($state['nav_items'] ?? [])
            ->filter(fn ($item) => filled($item['key'] ?? null))
            ->map(fn ($item) => [
                'key' => $item['key'],
                'label' => $item['label'] ?? ucfirst($item['key']),
            ]);

        $labels = $items->pluck('label', 'key')->all() + [
            'about_biodata' => $state['nav_about_biodata'] ?? 'Biodata & Personal Branding',
            'about_cv' => $state['nav_about_cv'] ?? 'Download CV',
            'about_interests' => $state['nav_about_interests'] ?? 'Interests & Hobbies',
            'projects_coding' => $state['nav_projects_coding'] ?? 'Coding Projects',
            'projects_experience' => $state['nav_projects_experience'] ?? 'Work Experience',
            'projects_contributions' => $state['nav_projects_contributions'] ?? 'Contributions & Activities',
        ];

        SiteSetting::updateOrCreate(['key' => 'navigation'], ['value' => [
            'order' => $items->pluck('key')->values()->all(),
            'labels' => $labels,
        ]]);

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
            'projects' => Project::with('images')->get()->toArray(),
            'skills' => Skill::with(['projectLinks', 'galleryItems'])->get()->toArray(),
            'experiences' => Experience::all()->toArray(),
            'contributions' => Contribution::all()->toArray(),
            'educations' => Education::all()->toArray(),
            'soft_skills' => SoftSkill::all()->toArray(),
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
