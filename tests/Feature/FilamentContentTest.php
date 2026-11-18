<?php

namespace Tests\Feature;

use App\Filament\Pages\HomePage;
use App\Filament\Pages\ProfilePage;
use App\Filament\Pages\SettingsPage;
use App\Filament\Resources\Contributions\Pages\CreateContribution;
use App\Filament\Resources\Educations\Pages\CreateEducation;
use App\Filament\Resources\SoftSkills\Pages\CreateSoftSkill;
use App\Models\Contribution;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FilamentContentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['email' => 'admin@example.com']);
    }

    #[Test]
    public function every_content_management_page_loads(): void
    {
        $admin = $this->admin();

        $pages = [
            '/admin',
            '/admin/home',
            '/admin/profile',
            '/admin/settings',
            '/admin/appearance',
            '/admin/education',
            '/admin/education/create',
            '/admin/interests',
            '/admin/skills',
            '/admin/skills/create',
            '/admin/soft-skills',
            '/admin/soft-skills/create',
            '/admin/projects',
            '/admin/projects/create',
            '/admin/experiences',
            '/admin/experiences/create',
            '/admin/contributions',
            '/admin/contributions/create',
            '/admin/contact-submissions',
            '/admin/socials',
            '/admin/gallery-images',
            '/admin/portfolio-messages',
        ];

        foreach ($pages as $page) {
            $this->actingAs($admin)
                ->get($page)
                ->assertOk();
        }
    }

    #[Test]
    public function admin_can_create_an_education_entry(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CreateEducation::class)
            ->fillForm([
                'institution' => 'Test University',
                'program' => 'Computer Science',
                'start_year' => '2020',
                'end_year' => '2024',
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('educations', [
            'institution' => 'Test University',
            'program' => 'Computer Science',
        ]);
    }

    #[Test]
    public function admin_can_create_a_soft_skill(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CreateSoftSkill::class)
            ->fillForm([
                'name' => 'Communication',
                'description' => 'Clear and kind updates.',
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('soft_skills', ['name' => 'Communication']);
    }

    #[Test]
    public function admin_can_create_a_contribution_with_links(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CreateContribution::class)
            ->fillForm([
                'name' => 'Community Guild',
                'role' => 'Mentor',
                'description' => 'Mentored builders.',
                'body' => '## Mentoring',
                'links' => [
                    ['label' => 'Profile', 'url' => 'https://example.com/profile'],
                ],
                'status' => 'published',
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $contribution = Contribution::query()->where('name', 'Community Guild')->first();

        $this->assertNotNull($contribution);
        $this->assertSame('community-guild', $contribution->slug);
        $this->assertSame('https://example.com/profile', $contribution->links[0]['url']);
    }

    #[Test]
    public function home_page_settings_can_be_saved(): void
    {
        Livewire::actingAs($this->admin())
            ->test(HomePage::class)
            ->fillForm([
                'hero_cta_primary_label' => 'See My Work',
                'hero_cta_primary_url' => '/projects',
                'hero_cta_secondary_label' => 'Say Hello',
                'hero_cta_secondary_url' => '/contact',
                'about_teaser' => 'A short teaser.',
                'contact_cta_heading' => 'Work With Me',
                'contact_cta_text' => 'Available for select projects.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $home = SiteSetting::home();

        $this->assertSame('See My Work', $home['hero_cta_primary_label']);
        $this->assertSame('A short teaser.', $home['about_teaser']);
        $this->assertSame('Work With Me', $home['contact_cta_heading']);
    }

    #[Test]
    public function site_settings_save_general_and_navigation(): void
    {
        Livewire::actingAs($this->admin())
            ->test(SettingsPage::class)
            ->fillForm([
                'site_title' => 'Rama — Portfolio',
                'meta_description' => 'Portfolio description.',
                'contact_email' => 'hello@example.com',
                'footer_text' => 'Built with care',
                'nav_items' => [
                    ['key' => 'home', 'label' => 'Start'],
                    ['key' => 'about', 'label' => 'About Me'],
                    ['key' => 'skills', 'label' => 'Skills'],
                    ['key' => 'projects', 'label' => 'Projects & Experience'],
                    ['key' => 'contact', 'label' => 'Contact'],
                ],
                'nav_about_biodata' => 'Biodata & Personal Branding',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Rama — Portfolio', SiteSetting::general()['site_title']);
        $this->assertSame('hello@example.com', SiteSetting::general()['contact_email']);

        $navigation = SiteSetting::navigation();
        $this->assertSame('Start', $navigation['labels']['home']);
        $this->assertSame('Biodata & Personal Branding', $navigation['labels']['about_biodata']);
        $this->assertSame(['home', 'about', 'skills', 'projects', 'contact'], $navigation['order']);
    }

    #[Test]
    public function profile_page_saves_branding_and_cv_fields(): void
    {
        Livewire::actingAs($this->admin())
            ->test(ProfilePage::class)
            ->fillForm([
                'name' => 'Rama',
                'fullName' => 'Rama Adin',
                'role' => 'Full Stack Developer',
                'branding_statement' => 'I build reliable products.',
                'cv_label' => 'Curriculum Vitae',
                'cv_version' => '2026.2',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('personal_info', [
            'key' => 'branding_statement',
            'value' => 'I build reliable products.',
        ]);

        $this->assertDatabaseHas('personal_info', [
            'key' => 'cv_version',
            'value' => '2026.2',
        ]);
    }
}
