<?php

namespace Tests\Feature;

use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function blog_index_lists_published_posts_only(): void
    {
        BlogPost::factory()->create([
            'title' => 'Catatan Hari Ini',
            'category' => 'Daily Note',
            'published_at' => now()->subDay(),
        ]);

        BlogPost::factory()->draft()->create(['title' => 'Tulisan Draf']);

        $this->get(route('about.blog'))
            ->assertOk()
            ->assertSee('Daily Blog')
            ->assertSee('Catatan Hari Ini')
            ->assertDontSee('Tulisan Draf');
    }

    #[Test]
    public function blog_detail_page_opens_by_slug(): void
    {
        $post = BlogPost::factory()->create([
            'title' => 'Debugging Malam Hari',
            'body' => "## Pelajaran\n\nBaca log sebelum menebak.",
            'published_at' => now()->subDay(),
        ]);

        $this->get(route('about.blog.show', $post))
            ->assertOk()
            ->assertSee('Debugging Malam Hari')
            ->assertSee('Baca log sebelum menebak.');
    }

    #[Test]
    public function draft_post_detail_returns_404(): void
    {
        $post = BlogPost::factory()->draft()->create();

        $this->get(route('about.blog.show', $post))->assertNotFound();
    }

    #[Test]
    public function blog_index_can_be_searched_and_filtered_by_category(): void
    {
        BlogPost::factory()->create(['title' => 'Belajar CSS Grid', 'category' => 'Devlog', 'published_at' => now()->subDay()]);
        BlogPost::factory()->create(['title' => 'Refleksi Setahun', 'category' => 'Reflection', 'published_at' => now()->subDays(2)]);

        $this->get(route('about.blog', ['q' => 'CSS Grid']))
            ->assertOk()
            ->assertSee('Belajar CSS Grid')
            ->assertDontSee('Refleksi Setahun');

        $this->get(route('about.blog', ['category' => 'Reflection']))
            ->assertOk()
            ->assertSee('Refleksi Setahun')
            ->assertDontSee('Belajar CSS Grid');
    }

    #[Test]
    public function home_shows_the_three_latest_posts(): void
    {
        foreach (range(1, 5) as $i) {
            BlogPost::factory()->create([
                'title' => "Tulisan Nomor {$i}",
                'published_at' => now()->subDays(10 - $i),
            ]);
        }

        $response = $this->get(route('home'))->assertOk();

        // Tiga terbaru (nomor 3, 4, 5) tampil, yang lama tidak.
        $response->assertSee('Tulisan Nomor 5');
        $response->assertSee('Tulisan Nomor 4');
        $response->assertSee('Tulisan Nomor 3');
        $response->assertDontSee('Tulisan Nomor 1');
        $response->assertDontSee('Tulisan Nomor 2');

        $this->assertSame(3, substr_count($response->getContent(), 'class="project-card reveal'));
    }

    #[Test]
    public function blog_index_paginates_after_nine_posts(): void
    {
        foreach (range(1, 12) as $i) {
            BlogPost::factory()->create([
                'title' => "Tulisan Harian {$i}",
                'published_at' => now()->subDays($i),
            ]);
        }

        $response = $this->get(route('about.blog'))->assertOk();

        // Halaman 1 hanya memuat 9 kartu, sisanya di halaman 2.
        $this->assertSame(9, substr_count($response->getContent(), 'class="project-card reveal'));
        $response->assertSee('Halaman 1 / 2');
        $response->assertSee('Lebih lama');
    }

    #[Test]
    public function about_menu_exposes_the_blog_link(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('about.blog'), false)
            ->assertSee('Daily Blog');
    }

    #[Test]
    public function sitemap_includes_blog_urls(): void
    {
        $post = BlogPost::factory()->create(['published_at' => now()->subDay()]);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('about.blog'), false)
            ->assertSee(route('about.blog.show', $post), false);
    }

    #[Test]
    public function admin_can_create_blog_post_via_filament(): void
    {
        $admin = User::factory()->create(['name' => 'Admin', 'email' => 'admin@example.com']);

        Livewire::actingAs($admin)
            ->test(CreateBlogPost::class)
            ->fillForm([
                'title' => 'Tulisan dari Admin',
                'category' => 'Daily Note',
                'excerpt' => 'Ringkasan singkat.',
                'body' => 'Isi lengkap tulisan.',
                'status' => 'published',
                'published_at' => now()->format('Y-m-d H:i:s'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = BlogPost::first();

        $this->assertNotNull($post);
        $this->assertSame('Tulisan dari Admin', $post->title);
        $this->assertSame('tulisan-dari-admin', $post->slug);

        // Tulisan baru langsung tampil di halaman publik.
        $this->get(route('about.blog'))->assertOk()->assertSee('Tulisan dari Admin');
    }
}
