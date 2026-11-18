# Rencana Migrasi Dashboard Admin → Filament 4

## Konteks

- **Sekarang**: `/admin.html` = SPA vanilla JS (16 file JS), auth client-side Sanctum token, backend lewat API admin di `routes/api.php:55-112`
- **Target**: Panel Filament 4 di `/admin`, session-based auth, semua 10 section + Contact Submissions
- **Dipertahankan**: frontend publik Blade + API publik GET (`routes/api.php:25-49`)
- **Stack**: Laravel 13.9, PHP 8.3, Tailwind CSS v4, Filament **4.x** (Livewire 3)

## Fase 1 — Install Filament 4

1. `composer require filament/filament:"~4.0"` (di PowerShell pakai `~`, bukan `^`)
2. `php artisan filament:install --panels` → panel ID `admin`
3. Pastikan `AdminPanelProvider` terdaftar di `bootstrap/providers.php`
4. Konfigurasi `AdminPanelProvider`:
   - Path `/admin`, brand "Portfolio Rama", warna brand (aksen existing)
   - `login()` saja — **tanpa registrasi**
   - User guard: `web` (sudah ada di `config/auth.php`, user `admin@example.com` langsung bisa login)
5. `php artisan storage:link` (symlink `/storage` belum ada — wajib untuk upload)

## Fase 2 — Resources & Pages (mapping 10 section + contact)

| Section lama | Implementasi Filament |
|---|---|
| **Overview** | Widget dashboard: stat cards (count Projects, Skills, Messages, Gallery, Contact) + quick actions |
| **Profile** | Custom **Page** `ProfilePage` — form PersonalInfo KV (name, role, bio, photo via `FileUpload`, dst) + About quote/stats; **Interests** → `InterestResource` |
| **Experience** | `ExperienceResource` — date string (`start_date`/`end_date` pakai TextInput dengan placeholder "Present" agar kompatibel frontend) |
| **Skills** | `SkillResource` + **RelationManagers**: `SkillProjectLinkRelationManager`, `SkillGalleryItemRelationManager` (upload ke `skill-gallery/`) |
| **Projects** | `ProjectResource` — `TagsInput` untuk `tech[]`, `FileUpload` thumbnail, `Repeater`/TagsInput gallery |
| **Socials** | `SocialResource` — toggle `visible`, `sort_order` |
| **Messages** | `PortfolioMessageResource` (read-heavy) — aksi bulk: hide/flag/pin/delete |
| **Gallery** | `GalleryImageResource` — `FileUpload` (migrasi dari base64 → file), kategori, toggle visible |
| **Appearance** | Custom **Page** — simpan accent color ke `site_settings` (sebelumnya localStorage-only; pindah ke server agar persist) |
| **Settings** | Custom **Page** — `SiteSetting` KV editor + ganti password (Filament profile page bawaan) + export data |
| **(baru)** Contact | `ContactSubmissionResource` — table read-only + delete |

Detail teknis per resource:
- **JSON columns** (`tech`, `gallery`, `related_projects`) → `TagsInput` / `Repeater`
- **Base64 images lama** (`projects.thumbnail`, `gallery_images.url`, `personal_info.value`): `FileUpload` menyimpan path file baru; accessor menampilkan base64 lama jika belum ada file (kompatibel mundur, tanpa migrasi data paksa)
- **`PortfolioMessage`**: `message_id` string unik → aksi table pakai `record->message_id`
- **`PersonalInfo` / `SiteSetting`** (KV): jangan pakai Resource generik — custom Page dengan form field per key
- **Urutan nav** di `AdminPanelProvider` mengikuti sidebar lama

## Fase 3 — Hapus Dashboard Lama

**File dihapus:**
- `routes/web.php:22-24` (route `/admin.html`) + `AdminController.php`
- `resources/views/admin/` (dashboard.blade.php), `resources/views/layouts/admin.blade.php`
- `public/js/`: `admin-*.js` (router, overview, profile, experience, skills, projects, socials, messages, gallery, appearance, settings, ui, config, auth) — **KECUALI** `api-client.js` & `data.js` (dipakai frontend publik)
- API admin di `routes/api.php:52` (`auth/login`) + grup `:55-112` + `messages/bulk` `:48`
- Controller API yang hanya dipakai admin: `AuthController`, `MediaController` (endpoint `POST media`); controller CRUD tetap dipertahankan untuk endpoint GET publik — method store/update/destroy boleh dibiarkan dulu atau dibersihkan (opsional)

**File diubah:**
- `layouts/portfolio.blade.php:31` — hapus include `admin-auth.js`
- `layouts/partials/navbar.blade.php` — ganti lock button/badge/dropdown jadi link sederhana ke `/admin` (login Filament)
- `bootstrap/app.php` — middleware `abilities` Sanctum bisa dipertahankan; package sanctum biarkan terpasang dulu (minim risiko), route saja yang dihapus

**Tests:**
- `tests/Feature/AdminPageTest.php` → ganti assertion ke `/admin` redirect ke login Filament
- `tests/Feature/Api/*` yang test endpoint admin → hapus/sesuaikan
- Tambah test feature: login Filament berhasil, resource CRUD basic

## Fase 4 — Verifikasi

1. `./vendor/bin/pint --test` / `php artisan test`
2. `npm run build` (publik tetap jalan)
3. Manual: login `/admin`, cek tiap resource CRUD + upload file, cek halaman publik masih normal

## Urutan Eksekusi

```
1. Install Filament + panel provider + storage:link
2. Dashboard widgets (overview)
3. Resource sederhana: Socials, Experience, Interests, Gallery, Contact
4. Resource sedang: Projects, Messages
5. Resource + RelationManager: Skills
6. Custom pages: Profile (PersonalInfo), Appearance, Settings
7. Hapus kode admin lama + update navbar
8. Perbaiki tests, pint, build
```

## Risiko / Catatan

- **Tailwind v4**: Filament 4 sendiri sudah bundle CSS-nya — tidak bentrok dengan theme custom
- **Base64 lama**: ditangani aksesor kompatibel, tidak perlu migrasi data paksa
- **`PortfolioDatabaseSeeder`** sudah rusak (model `PortfolioData` hilang) — di luar scope, tidak disentuh
- Estimasi: ~15 file baru (resources/pages/widgets) + ~10 file dihapus/diubah

## Ringkasan File Kunci

| Area | File |
|---|---|
| Provider panel | `app/Providers/Filament/AdminPanelProvider.php` (baru) |
| Resources | `app/Filament/Resources/*.php` (baru) |
| Pages custom | `app/Filament/Pages/*.php` (baru) |
| Routes lama | `routes/web.php`, `routes/api.php` |
| View lama | `resources/views/admin/`, `resources/views/layouts/admin.blade.php` |
| JS lama | `public/js/admin-*.js` |
| Navbar publik | `resources/views/layouts/partials/navbar.blade.php` |
| Layout publik | `resources/views/layouts/portfolio.blade.php` |
| Model | `app/Models/` (13 model, tidak diubah) |
| Migrasi | `database/migrations/` (tidak diubah) |
