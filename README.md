# Portfolio Rama

A personal portfolio site built with **Laravel 13**, **Filament 4**, **Tailwind CSS v4**, and Blade. The public pages follow the navigation structure in [`PORTFOLIO_NAVIGATION_SPEC (1).md`](PORTFOLIO_NAVIGATION_SPEC%20(1).md); every piece of content is managed from the existing Filament admin panel at `/admin` — no code changes or redeploys are needed to update the site.

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13 / PHP 8.3 |
| Admin panel | Filament 4 (Livewire 3) |
| Frontend | Blade + Tailwind CSS v4 + Vite |
| Database | MySQL (SQLite in tests) |
| Media | Laravel public disk (`storage/app/public`) |

## Public navigation

```
Home                            /
About Me                        dropdown
 ├── Biodata & Personal Branding /about/biodata
 ├── Download CV                /about/cv
 ├── Interests & Hobbies        /about/interests
 └── Daily Blog                 /about/blog      (+ /about/blog/{slug})
Skills                          /skills
Projects & Experience           dropdown
 ├── Coding Projects             /projects        (+ /projects/{slug})
 ├── Work Experience             /experience      (+ /experience/{slug})
 └── Contributions & Activities  /contributions   (+ /contributions/{slug})
Contact                         /contact
```

Projects and experience entries are rendered as blog-style articles with Markdown bodies, cover images, tags, publish dates, and previous/next navigation.

SEO: every page emits a canonical URL, meta description, Open Graph/Twitter tags and Person JSON-LD; `/sitemap.xml` lists all public pages plus published articles, and `/robots.txt` (served dynamically) points to it and blocks `/admin`. Unpublished entries return the styled 404 page.

## Content management (`/admin`)

| Sidebar group | Manages |
|---|---|
| **Home** | Home page hero buttons (termasuk **Download CV** otomatis dari CV yang diunggah), about teaser, contact call-to-action, home gallery, guestbook messages |
| **About Me** | Profile & personal branding, profile photos, CV upload/label/version, education timeline, interests, **daily blog posts** |
| **Skills** | Skill type (Tech Stack = coding, Other = non-coding hard skills like Excel/Design), category, level, proficiency, featured flag; plus soft skills |
| **Projects & Experience** | Coding projects (article body, gallery, tech tags, links, featured flag, draft/published), work experience, contributions |
| **Contact** | Contact form submissions (read/unread, delete), social links |
| **Site Settings** | Site title, meta description, Open Graph image, contact email, footer, navigation labels/order, appearance |

Notes:

- Rich text fields use Filament's Markdown editor. Public pages render Markdown with raw HTML stripped.
- Image uploads accept JPEG/PNG/WebP/GIF up to 5 MB; the CV upload accepts PDF up to 10 MB.
- Slugs are generated automatically from the title when left empty, and remain editable.
- Draft entries (projects, experience, contributions) and future `published_at` values are hidden from the public site.
- Featured projects and highlighted tech stack on the home page are chosen with the **Featured on Home** / **Highlight on Home Page** toggles.
- Contoh tulisan blog: `php artisan db:seed --class=BlogPostSeeder` (3 tulisan, aman diulang karena memakai slug sebagai kunci).
- **Daily Blog** (`/admin/blog-posts`): judul + slug otomatis, kategori (Daily Note, Devlog, Tutorial, Reflection), ringkasan, isi Markdown, cover 16:9, tag, status draft/published. Tulisan published tampil di `/about/blog` (dengan pencarian, filter kategori, dan pagination 9 per halaman) dan **3 tulisan terbaru** muncul di beranda pada bagian “Catatan Terbaru”. Kalau ringkasan dikosongkan, kartu memakai potongan isi tulisan; kalau cover kosong, muncul kotak inisial judul.

## Local setup

```bash
composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate

# configure DB_* in .env, then:
php artisan migrate --seed
php artisan storage:link
php artisan filament:assets   # publishes the admin panel CSS/JS/fonts to public/

npm install
npm run build

php artisan serve
```

The site is available at `http://127.0.0.1:8000`, the admin panel at `/admin`.

The seeder creates an admin account (`admin@example.com` / `admin123` — **change it**), placeholder portfolio content, and a placeholder CV PDF that can be replaced from **About Me → Profile → CV / Resume**.

## Tests and quality checks

```bash
php artisan test          # feature + unit tests
./vendor/bin/pint --test  # code style
npm run build             # production assets
```

## Tema (terang / gelap)

Seluruh warna, font, radius, bayangan dan spasi berasal dari token di `public/css/variables.css`.

- Mode terang (default) memakai palet krem–charcoal–emas; mode gelap didefinisikan ulang di blok `:root[data-theme="dark"]` pada file yang sama.
- Script kecil di `<head>` (`resources/views/layouts/portfolio.blade.php`) memasang `data-theme` sebelum paint supaya tidak ada kedipan, dan tetap menghormati `accentColor` dari panel admin.
- Tombol pengalih ada di navbar (`.navbar__theme`) dan di menu mobile; logikanya di `public/js/theme.js`, pilihannya disimpan di `localStorage` dengan kunci `portfolio_theme`, dan mengikuti preferensi sistem bila pengguna belum memilih.
- Tipografi: **Space Grotesk** untuk heading/label (`--font-display`, `--font-mono`), **Inter** untuk teks (`--font-body`), dan **JetBrains Mono** khusus blok kode (`--font-code`). Font dimuat dari Google Fonts di `layouts/portfolio.blade.php`.
- Menambah warna baru: tambahkan token di kedua blok (terang & gelap) lalu pakai `var(--token)` — jangan menulis hex langsung di komponen.
- Setelah mengubah CSS, jalankan `npm run build` (halaman memuat CSS dari `public/build` via `@vite`).

## Project structure

```
app/
  Filament/                 # Admin panel pages, resources, widgets
  Http/Controllers/         # Public PortfolioController + legacy read API
  Models/                   # Eloquent models (Project, Experience, Contribution, ...)
  Support/Navigation.php    # Builds the public navigation from site settings
database/
  migrations/               # Schema + default content backfills
  seeders/                  # PortfolioSeeder + PortfolioContentSeeder
resources/
  views/portfolio/          # Public Blade pages
  views/layouts/            # Shared layout, navbar, footer
  css/app.css               # Tailwind entry + component styles
routes/web.php              # Public page routes
```
