<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    private function adminToken(): string
    {
        $user = User::factory()->create();
        return $user->createToken('admin-token', ['admin:write'])->plainTextToken;
    }

    private function tokenWithoutAbility(): string
    {
        $user = User::factory()->create();
        return $user->createToken('no-ability-token', [])->plainTextToken;
    }

    // ====== HAPPY PATH ======

    #[Test]
    public function guest_cannot_upload()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.jpg', 200, 200);

        $response = $this->postJson('/api/media', [
            'image' => $file,
        ]);

        $response->assertUnauthorized();
    }

    #[Test]
    public function token_without_admin_ability_cannot_upload()
    {
        Storage::fake('public');

        $token = $this->tokenWithoutAbility();
        $file = UploadedFile::fake()->image('photo.jpg', 200, 200);

        $response = $this->withToken($token)
            ->postJson('/api/media', ['image' => $file]);

        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_upload_to_default_profile_folder()
    {
        Storage::fake('public');

        $token = $this->adminToken();
        $file = UploadedFile::fake()->image('hero.png', 800, 600);

        $response = $this->withToken($token)
            ->postJson('/api/media', ['image' => $file]);

        $response->assertCreated()
            ->assertJsonStructure(['path', 'url']);

        $path = $response->json('path');
        $this->assertStringStartsWith('uploads/profile/', $path);
        $this->assertStringEndsWith('.png', $path);

        $this->assertEquals('/storage/' . $path, $response->json('url'));

        Storage::disk('public')->assertExists($path);
    }

    #[Test]
    public function admin_can_upload_to_gallery_folder()
    {
        Storage::fake('public');

        $token = $this->adminToken();
        $file = UploadedFile::fake()->image('gallery.jpg', 1920, 1080);

        $response = $this->withToken($token)
            ->postJson('/api/media', [
                'image' => $file,
                'folder' => 'gallery',
            ]);

        $response->assertCreated();
        $path = $response->json('path');

        $this->assertStringStartsWith('uploads/gallery/', $path);
        Storage::disk('public')->assertExists($path);
    }

    #[Test]
    public function admin_can_upload_to_projects_folder()
    {
        Storage::fake('public');

        $token = $this->adminToken();
        $file = UploadedFile::fake()->image('project.webp', 1200, 900);

        $response = $this->withToken($token)
            ->postJson('/api/media', [
                'image' => $file,
                'folder' => 'projects',
            ]);

        $response->assertCreated();
        $this->assertStringStartsWith('uploads/projects/', $response->json('path'));
    }

    // ====== FAILURE CASES ======

    #[Test]
    public function it_rejects_upload_without_image()
    {
        Storage::fake('public');

        $token = $this->adminToken();

        $response = $this->withToken($token)
            ->postJson('/api/media', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    #[Test]
    public function it_rejects_invalid_file_type()
    {
        Storage::fake('public');

        $token = $this->adminToken();
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->withToken($token)
            ->postJson('/api/media', ['image' => $file]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    #[Test]
    public function it_rejects_too_large_file()
    {
        Storage::fake('public');

        $token = $this->adminToken();
        $file = UploadedFile::fake()->create('huge.jpg', 11264, 'image/jpeg');

        $response = $this->withToken($token)
            ->postJson('/api/media', ['image' => $file]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    #[Test]
    public function it_rejects_invalid_folder_name()
    {
        Storage::fake('public');

        $token = $this->adminToken();
        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->withToken($token)
            ->postJson('/api/media', [
                'image' => $file,
                'folder' => 'invalid_folder',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['folder']);
    }

    // ====== EDGE CASES ======

    #[Test]
    public function it_accepts_uppercase_extensions()
    {
        Storage::fake('public');

        $token = $this->adminToken();
        $file = UploadedFile::fake()->image('photo.JPG', 100, 100);

        $response = $this->withToken($token)
            ->postJson('/api/media', ['image' => $file]);

        $response->assertCreated();
        $path = $response->json('path');
        Storage::disk('public')->assertExists($path);
    }

    #[Test]
    public function it_rejects_folder_with_subdirectory_traversal()
    {
        Storage::fake('public');

        $token = $this->adminToken();
        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->withToken($token)
            ->postJson('/api/media', [
                'image' => $file,
                'folder' => 'profile/../../etc',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['folder']);
    }

    #[Test]
    public function it_supports_webp_and_gif_formats()
    {
        Storage::fake('public');

        $token = $this->adminToken();

        $webp = UploadedFile::fake()->image('anim.webp', 100, 100);
        $response = $this->withToken($token)
            ->postJson('/api/media', [
                'image' => $webp,
                'folder' => 'gallery',
            ]);
        $response->assertCreated();
        Storage::disk('public')->assertExists($response->json('path'));

        $gif = UploadedFile::fake()->image('anim.gif', 100, 100);
        $response = $this->withToken($token)
            ->postJson('/api/media', [
                'image' => $gif,
                'folder' => 'gallery',
            ]);
        $response->assertCreated();
        Storage::disk('public')->assertExists($response->json('path'));
    }

    #[Test]
    public function it_returns_unique_paths_for_two_uploads()
    {
        Storage::fake('public');

        $token = $this->adminToken();

        $file1 = UploadedFile::fake()->image('same.jpg', 100, 100);
        $response1 = $this->withToken($token)
            ->postJson('/api/media', ['image' => $file1]);

        $file2 = UploadedFile::fake()->image('same.jpg', 100, 100);
        $response2 = $this->withToken($token)
            ->postJson('/api/media', ['image' => $file2]);

        $response1->assertCreated();
        $response2->assertCreated();

        $this->assertNotEquals($response1->json('path'), $response2->json('path'));
        Storage::disk('public')->assertExists($response1->json('path'));
        Storage::disk('public')->assertExists($response2->json('path'));
    }
}


/*
 * ====================================================================
 * TESTABILITY ANALYSIS
 * ====================================================================
 *
 * The MediaController::store() method is well-structured overall.
 * Below are the findings:
 *
 * 1. Hardcoded 'public' disk — LOC: line 21
 *    Problem: The disk name 'public' is hardcoded inside store().
 *    If the app switches to S3 or a different disk, this breaks.
 *    Suggestion: Inject the disk name via config or constructor.
 *    Before:
 *        $path = $request->file('image')->store("uploads/{$folder}", 'public');
 *    After:
 *        $disk = config('filesystems.media_disk', 'public');
 *        $path = $request->file('image')->store("uploads/{$folder}", $disk);
 *
 * 2. Folder default is hardcoded — LOC: line 20
 *    Problem: $folder = $validated['folder'] ?? 'profile';
 *    This is minor, but if the frontend always sends a folder, the default
 *    is dead code. Consider making this a configurable default or removing
 *    the default if all callers specify a folder.
 *
 * 3. No event dispatched after upload — LOC: line 13-27
 *    Problem: After a successful upload, no event (e.g., ImageUploaded) is
 *    dispatched. Any follow-up logic (thumbnails, CDN invalidation, logging)
 *    must be coupled directly into this method.
 *    Suggestion: dispatch(new ImageUploaded($path, $validated['folder']));
 *    This keeps the controller lean and allows listeners to handle
 *    post-processing without modifying the controller.
 *
 * ====================================================================
 */
