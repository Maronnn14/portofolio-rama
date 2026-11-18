<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

/**
 * Tiga contoh tulisan blog harian.
 *
 * Jalankan: php artisan db:seed --class=BlogPostSeeder
 * Aman diulang: memakai slug sebagai kunci (updateOrCreate), jadi tidak menduplikasi.
 */
class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'slug' => 'menata-ulang-warna-jadi-token-css',
                'title' => 'Menata ulang warna jadi token CSS',
                'category' => 'Daily Note',
                'tags' => ['CSS', 'Refactor', 'Dark Mode'],
                'published_at' => now()->subDay()->setTime(20, 15),
                'excerpt' => 'Semua hex yang berserakan di 12 file saya tarik ke satu tempat. Sekarang mode gelap cukup menimpa satu blok.',
                'body' => <<<'MD'
## Masalahnya

Setiap komponen menulis warnanya sendiri: `#faf8f5` di sini, `#16130f` di sana. Begitu tema berubah, ada puluhan tempat yang harus diedit dan pasti ada satu yang terlewat.

## Yang saya lakukan

- Mendata semua warna unik yang dipakai
- Mengelompokkannya jadi token semantik: `--bg-primary`, `--text-muted`, `--accent`
- Mengganti nilai lama dengan `var(--token)` di seluruh komponen

```css
:root {
  --bg-primary: #faf8f5;
  --accent: #7d5f30;
}

:root[data-theme="dark"] {
  --bg-primary: #16130f;
  --accent: #d9bc8c;
}
```

## Hasilnya

Mode gelap sekarang cukup satu blok tambahan, tanpa menyentuh satu pun file komponen. Warna baru cukup didaftarkan sekali, lalu dipakai di mana saja memakai nama tokennya.

> Pelajaran: refactor kecil yang membosankan sering jadi fondasi fitur besar berikutnya.
MD,
            ],
            [
                'slug' => 'devlog-menutup-celah-di-guestbook',
                'title' => 'Devlog: menutup celah di guestbook',
                'category' => 'Devlog',
                'tags' => ['Laravel', 'Keamanan'],
                'published_at' => now()->subDays(4)->setTime(21, 40),
                'excerpt' => 'Satu baris payload API ternyata cukup untuk membuat pengunjung bisa menyunting pesan orang lain. Ini cara saya menutupnya.',
                'body' => <<<'MD'
## Temuannya

Respons daftar pesan ikut mengirim token sesi milik setiap pengunjung. Siapa pun yang membuka tab Network bisa memakai token itu untuk mengubah atau menghapus pesan lain.

## Perbaikan

- Token tidak lagi dibuat di klien maupun dikirim balik ke publik
- Edit dan hapus hanya bisa dilakukan admin lewat panel, bukan lewat API publik
- Tombol aksi di kartu pesan dihapus supaya antarmuka jujur soal apa yang boleh dilakukan

## Catatan

Pengecekan izin tidak boleh tinggal di JavaScript. Browser bisa dimanipulasi; server tidak. Apa pun yang disembunyikan di UI tetap harus ditolak di sisi server.
MD,
            ],
            [
                'slug' => 'pelajaran-dari-review-kode-sendiri',
                'title' => 'Pelajaran dari review kode sendiri',
                'category' => 'Reflection',
                'tags' => ['Karier', 'Kebiasaan'],
                'published_at' => now()->subDays(8)->setTime(19, 5),
                'excerpt' => 'Membaca ulang kode tiga bulan lalu itu menyakitkan, tapi di situlah catatan paling jujur muncul.',
                'body' => <<<'MD'
## Tiga kebiasaan yang paling sering terulang

1. Nama variabel yang terlalu singkat sehingga perlu dibaca ulang konteksnya
2. Fungsi yang mengerjakan dua hal sekaligus, jadi sulit diuji
3. Komentar yang menjelaskan *apa* yang dilakukan, bukan *kenapa* diputuskan begitu

## Yang saya ubah

Mulai sekarang setiap kali menyentuh sebuah fungsi, saya sisakan satu perbaikan kecil: nama yang lebih jelas, satu tanggung jawab, atau komentar alasan.

Tidak ada yang dramatis. Tapi tiga bulan lagi, orang yang membaca kode ini adalah saya sendiri.
MD,
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(
                ['slug' => $post['slug']],
                $post + ['status' => 'published'],
            );
        }

        $this->command?->info('BlogPostSeeder: '.count($posts).' tulisan contoh siap.');
    }
}
