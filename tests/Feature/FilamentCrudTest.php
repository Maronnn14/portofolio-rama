<?php

namespace Tests\Feature;

use App\Filament\Pages\ProfilePage;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Socials\Pages\CreateSocial;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FilamentCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
    }

    #[Test]
    public function admin_can_create_project_via_filament(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(CreateProject::class)
            ->fillForm([
                'name' => 'Filament Project',
                'short_desc' => 'Created through Filament',
                'full_desc' => 'Full description',
                'category' => 'Full Stack',
                'status' => 'published',
                'tech' => ['Laravel', 'Filament'],
                'featured' => true,
                'sort_order' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', [
            'name' => 'Filament Project',
            'category' => 'Full Stack',
            'featured' => true,
        ]);
    }

    #[Test]
    public function admin_can_edit_project_via_filament(): void
    {
        $admin = $this->admin();
        $project = Project::factory()->create(['name' => 'Old Name']);

        Livewire::actingAs($admin)
            ->test(EditProject::class, ['record' => $project->getKey()])
            ->fillForm([
                'name' => 'Updated Name',
                'short_desc' => 'Updated short description',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', [
            'id' => $project->getKey(),
            'name' => 'Updated Name',
        ]);
    }

    #[Test]
    public function admin_can_create_social_via_filament(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(CreateSocial::class)
            ->fillForm([
                'platform' => 'GitHub',
                'url' => 'https://github.com/example',
                'label' => 'GitHub',
                'icon' => 'github',
                'visible' => true,
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('socials', [
            'platform' => 'GitHub',
            'url' => 'https://github.com/example',
        ]);
    }

    #[Test]
    public function profile_page_saves_personal_info(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(ProfilePage::class)
            ->fillForm([
                'name' => 'Rama',
                'fullName' => 'Rama Adin',
                'role' => 'Full Stack Developer',
                'shortBio' => 'Building things',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('personal_info', [
            'key' => 'name',
            'value' => 'Rama',
        ]);

        $this->assertDatabaseHas('personal_info', [
            'key' => 'fullName',
            'value' => 'Rama Adin',
        ]);
    }

    #[Test]
    public function project_form_handles_external_thumbnail_url(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(CreateProject::class)
            ->fillForm([
                'name' => 'External Thumb Project',
                'short_desc' => 'Uses external image',
                'external_thumbnail' => 'https://example.com/thumb.jpg',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $project = Project::where('name', 'External Thumb Project')->first();

        $this->assertNotNull($project);
        $this->assertSame('https://example.com/thumb.jpg', $project->getRawOriginal('thumbnail'));
    }
}
