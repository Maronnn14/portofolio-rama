# DESIGN SPEC — Portofolio "Rafael Creative"

> Dokumen ini adalah **sumber kebenaran tunggal** untuk membangun halaman portofolio.
> Ikuti nilai angka, warna, dan urutan persis seperti tertulis. Jika ada yang tidak
> ditentukan, pilih opsi paling sederhana yang konsisten dengan filosofi visual di bawah,
> lalu catat keputusan itu di bagian "Catatan Asumsi" pada jawaban akhir.

---

## 0. Ringkasan Tugas

| Item | Nilai |
|---|---|
| Jenis output | 1 halaman landing/portofolio (hero + galeri proyek) |
| Teknologi default | HTML + CSS + JavaScript vanilla, **satu file `index.html`** (CSS & JS inline) |
| Bahasa antarmuka | Inggris untuk label UI; Indonesia untuk catatan/komentar kode |
| Pemilik | Rafael (Rafael Creative), Banjarmasin, ID |
| Peran | Digital Illustrator / UI Designer |
| Tanpa dependensi | Hanya boleh Google Fonts (lihat §3). Tanpa framework, tanpa library animasi |

---

## 1. Konsep Utama & Filosofi Visual

Memadukan seni digital/anime berkualitas tinggi dengan tampilan **galeri seni modern yang
tenang dan lapang**. Lima referensi diterjemahkan ke palet **krem–charcoal–emas lembut**.

**Pengaruh referensi (ambil elemen struktural, bukan warnanya):**

| Referensi | Yang diambil |
|---|---|
| Bocchi the Rock | Struktur halaman: tab, kartu episode, tombol pil |
| Violet Evergarden | Serif elegan, pemisah diagonal |
| Kimono Party | Layout bersih, tombol CTA lembut, ikon sosial kiri bawah |
| "Glasses" | Tipografi raksasa di belakang karakter |
| Blue Archive | Tekstur kertas, stiker potongan karakter (outline putih) |

**ATURAN KERAS:** warna biru dan merah pekat dari referensi **tidak boleh dipakai**.
Semua diadaptasi ke krem, charcoal, dan emas lembut.

### Tata letak
- **Asymmetrical split-screen:** ±45% kiri (karakter), ±55% kanan (konten).
- Batas dua sisi = **garis diagonal halus** sudut **15–20°** (pakai `clip-path` polygon atau
  pseudo-element ber-`transform: skewX`).
- Sisi kiri `#FAF8F5`, sisi kanan `#F3EEE6` (kontras lembut).
- **Ruang negatif minimal 30–40%** dari total area.
- Padding horizontal **64–96 px** (desktop), jarak antar blok **40–56 px**.
- Karakter **boleh memotong batas diagonal** (bahu/rambut sedikit masuk ke sisi kanan)
  → gunakan `z-index` lebih tinggi dan `overflow: visible`.

---

## 2. Palet Warna (Design Tokens)

```css
:root {
  --cream:        #FAF8F5; /* background utama, kanvas kertas galeri */
  --cream-deep:   #F3EEE6; /* sisi kanan split, kartu, footer */
  --charcoal:     #4A4A4A; /* judul, paragraf, navigasi */
  --warm-gray:    #8A857D; /* label kecil, caption, metadata */
  --gold:         #B69A68; /* angka statistik, garis tipis, ikon bintang, indikator aktif */
  --burgundy:     #7A2E3A; /* HANYA di dalam ilustrasi / overlay hover kartu */

  --gold-line:    rgba(182, 154, 104, 0.35); /* border & pemisah (opasitas 30–40%) */
  --shadow-soft:  0 24px 60px rgba(74, 58, 40, 0.08); /* blur besar, opasitas <10%, hangat */
  --ease:         cubic-bezier(0.22, 0.61, 0.36, 1); /* ease-out */
  --dur:          280ms; /* 250–300 ms */
}
```

Aturan pemakaian:
- Krem = kanvas, charcoal = keterbacaan, emas = **aksen saja** (jangan jadi warna teks paragraf).
- Border/pemisah selalu `--gold-line`.
- Bayangan **tidak boleh hitam murni**; selalu hangat, blur besar, opasitas < 10%.
- `--burgundy` dilarang untuk UI umum. Hanya overlay hover kartu (opasitas 10–15%).

---

## 3. Tipografi

Google Fonts yang dipakai:
`Cormorant Garamond` (600/700), `Inter` (400/500/600). Opsional alternatif: `Playfair Display`, `Anton`, `Archivo Black`, `Poppins`.

| Elemen | Font | Ukuran | Catatan |
|---|---|---|---|
| Judul utama "RAFAEL CREATIVE" | Cormorant Garamond (alt: Playfair Display / Anton / Archivo Black) | **72–96 px** (`clamp(48px, 7vw, 96px)`) | UPPERCASE, line-height **0.95–1.05**, dipecah **2 baris** ("RAFAEL" / "CREATIVE") |
| Ornamen judul | SVG inline | ±0.5em | Bintang **berujung empat** di dalam huruf **O**, warna emas |
| Teks tubuh | Inter (alt: Poppins) | **14–16 px** | line-height **1.7–1.8**, letter-spacing **+0.02 s/d +0.05em** |
| Label kecil & navigasi | Inter | **12–13 px** | UPPERCASE, letter-spacing **+0.12em** |
| Meta editorial sudut | Inter | **10–11 px** | UPPERCASE, `--warm-gray` |
| Angka statistik | Cormorant Garamond | **48 px** | `--gold` |

Hierarki: judul besar → subjudul → paragraf → label meta.

---

## 4. Struktur & Komponen Halaman

Lima blok: **(A)** header, **(B)** hero kiri, **(C)** hero kanan, **(D)** galeri proyek,
**(E)** meta editorial sudut.

### A. Header / Navigasi Atas
- Tinggi **72–80 px**, background **transparan** menyatu dengan hero.
- **Kiri:** monogram **"RC"** garis tipis emas (SVG), atau ikon panah kembali.
- **Tengah/kanan:** `About`, `Portfolio`, `Contact`, jarak antar item **≥ 40 px**.
  Item aktif = **garis bawah tipis emas** (bukan tombol).
- **Ujung kanan:** ikon pencarian ATAU avatar bulat kecil (opsional, minimalis).

### B. Hero Kiri (Karakter)
- Ilustrasi avatar/karya andalan **besar, setengah badan, dipotong dramatis**
  (tangan/instrumen keluar frame).
- **Outline putih tipis ala stiker** (Blue Archive) → `filter: drop-shadow(0 0 0 #fff)` berlapis
  atau beberapa `drop-shadow(±2px)` putih, plus bayangan halus di bawahnya.
- **Watermark tipografi raksasa** di belakang karakter: kata `ILLUSTRATOR` atau `CREATIVE`,
  **200–280 px**, warna charcoal/emas **opasitas 6–10%**. Karakter menimpa sebagian huruf.
- Versi **blur** karakter yang sama di latar, opasitas sangat rendah (≤ 8%).
- **Tekstur noise/grain kertas** halus, opasitas **3–5%** (overlay SVG `feTurbulence`
  atau data-URI, `pointer-events: none`).

### C. Hero Kanan (Konten) — urutan atas → bawah
1. **Pill/tag kecil** (`Digital Illustrator`, `UI Designer`): outline tipis, border emas,
   teks uppercase **11 px**.
2. **Judul besar** 2 baris (lihat §3).
3. **Tab mini** (`About`, `Skills`, `Works`): satu tab aktif bergaris emas.
   *Boleh dihilangkan bila terasa padat.*
4. **Garis horizontal** 1 px, `--gold-line`.
5. **Paragraf About Me:** 2 paragraf pendek, **lebar maks 460–520 px**, warna charcoal;
   menyorot standar profesional: **detail, konsistensi, ketepatan deadline**.
6. **Blok statistik** 2 kolom, garis vertikal tipis di antaranya:
   - **5+** — YEARS EXPERIENCE
   - **50+** — COMPLETED PROJECTS
   Angka serif 48 px emas; label 12 px uppercase abu hangat.
7. **CTA:** tautan `Explore Portfolio →` + **tombol pil besar** (tinggi **48–52 px**),
   charcoal dengan teks krem (atau emas lembut + bayangan tipis).
8. **Ikon sosial** kecil: Instagram, Behance, X — posisi **kiri bawah** area konten.

### D. Galeri Proyek (Sudut Kanan Bawah)
- Label kecil **"Featured Works"**.
- **2–3 kartu** ±**180×110 px**, radius **16–20 px**. Kartu pertama utuh; kartu berikutnya
  **terpotong di tepi layar** (petunjuk bisa digeser).
- Tiap kartu: thumbnail, label pojok bawah-kiri (`WORK 01`, `WORK 02`, …),
  **ikon play/gallery bulat** di tengah (hanya kartu aktif).
- Kartu aktif: **border emas tipis**; kartu lain **opasitas 70%**.
- **Panah kiri/kanan** dalam kotak tipis di pojok bawah; berfungsi menggeser carousel
  (`scroll-snap` + tombol, atau JS sederhana).

### E. Meta Editorial (4 sudut hero, teks 10–11 px)
| Posisi | Isi |
|---|---|
| Kiri atas | `Portfolio 2026` |
| Kanan atas | `Banjarmasin, ID` |
| Kiri bawah | Label dwibahasa kecil, mis. `Ilustrator Digital / Digital Illustrator` |
| Kanan bawah | `Designed by Rafael` |

---

## 5. Interaksi & Efek (durasi 250–300 ms, easing ease-out)

| Elemen | Perilaku |
|---|---|
| Hover tombol | naik **2 px** (`translateY(-2px)`), bayangan membesar lembut |
| Hover kartu proyek | gambar **zoom 1.04×**, border emas muncul, ikon play membesar sedikit, **overlay marun 10–15%** (satu-satunya tempat marun di UI) |
| Hover navigasi | garis bawah emas **tumbuh kiri → kanan** (`transform: scaleX` dengan `transform-origin: left`) |
| Watermark latar | **parallax sangat lambat**, 10–15% kecepatan scroll |
| Animasi masuk | karakter **fade-in + geser dari kiri**; teks muncul berurutan **stagger 80–100 ms**: judul → paragraf → statistik → tombol |
| Hitung angka | statistik menghitung **0 → 5+** dan **0 → 50+** saat masuk viewport (`IntersectionObserver`, sekali jalan) |
| Kursor (opsional) | titik kecil emas, membesar di atas kartu; **nonaktif di perangkat sentuh** |

---

## 6. Responsif & Aksesibilitas

**Breakpoint:**
| Lebar | Perilaku |
|---|---|
| Desktop ≥ 1024 px | Layout split diagonal penuh |
| Tablet 640–1023 px | Split berubah jadi **atas-bawah** (karakter atas, konten bawah); galeri jadi **carousel horizontal** |
| Mobile < 640 px | **Satu kolom**; watermark dikecilkan; navigasi → **ikon hamburger**; statistik **tetap 2 kolom** |

**Aksesibilitas (wajib):**
- Kontras teks charcoal di atas krem memenuhi **WCAG AA**. Emas hanya untuk elemen dekoratif
  atau teks besar (≥ 24 px) — **jangan** pakai emas untuk teks kecil.
- Semua gambar punya `alt` yang bermakna; elemen dekoratif (`watermark`, grain, blur) pakai
  `alt=""` / `aria-hidden="true"`.
- Hormati `prefers-reduced-motion: reduce` → matikan parallax, stagger, count-up (tampilkan
  angka final langsung), dan transisi besar.
- Navigasi bisa dioperasikan keyboard (`:focus-visible` dengan outline emas jelas),
  gunakan elemen semantik (`<header>`, `<nav>`, `<main>`, `<section>`, `<button>`).
- Hamburger mobile: `aria-expanded`, `aria-controls`, tutup dengan `Esc`.
- Carousel: tombol panah punya `aria-label`.

---

## 7. Aset & Placeholder

Aset gambar **tidak disediakan** dalam dokumen ini. Aturan:
- Jangan mengarang URL gambar eksternal. Gunakan **placeholder lokal**: SVG inline siluet
  karakter setengah badan (palet krem/charcoal/emas), dan 3 thumbnail berupa gradient/SVG
  sederhana berlabel `WORK 01–03`.
- Tandai tiap placeholder dengan komentar `<!-- GANTI: ... -->` beserta rasio/ukuran ideal
  (karakter ±800×1100 px PNG transparan; thumbnail 360×220 px).
- Jangan menggambar atau meniru karakter/maskot berhak cipta dari referensi anime. Karakter
  placeholder harus **orisinal/generik**.

---

## 8. Struktur Output yang Diharapkan

```
index.html        ← semua HTML + <style> + <script> (self-contained)
```

Urutan isi file: `:root` tokens → reset/base → layout split → komponen A–E → interaksi →
media queries → `prefers-reduced-motion` → script (count-up, parallax, carousel, hamburger,
reveal on scroll).

Kualitas kode: HTML semantik, CSS memakai variabel dari §2, tanpa `!important`,
JS tanpa library, komentar singkat per blok.

---

## 9. Checklist Penerimaan (verifikasi sebelum menjawab selesai)

**Visual**
- [ ] Split ±45/55 dengan batas diagonal 15–20°; kiri `#FAF8F5`, kanan `#F3EEE6`
- [ ] Tidak ada biru atau merah pekat di UI (marun hanya di overlay hover kartu/ilustrasi)
- [ ] Judul 2 baris uppercase 72–96 px, ada bintang 4 ujung di huruf O
- [ ] Watermark raksasa 200–280 px, opasitas 6–10%, tertimpa karakter
- [ ] Outline stiker putih pada karakter + grain 3–5%
- [ ] Statistik **5+** dan **50+** dengan garis vertikal tipis di antaranya
- [ ] 2–3 kartu galeri; kartu aktif bergaris emas, lainnya 70%; kartu terakhir terpotong
- [ ] Keempat meta sudut hadir dengan teks yang benar

**Interaksi**
- [ ] Hover tombol, kartu, navigasi sesuai §5
- [ ] Count-up jalan sekali saat terlihat
- [ ] Animasi masuk berurutan 80–100 ms
- [ ] Parallax watermark 10–15%

**Responsif & A11y**
- [ ] Desktop / tablet / mobile sesuai §6 (tidak ada scroll horizontal tak disengaja)
- [ ] `prefers-reduced-motion` ditangani
- [ ] Fokus keyboard terlihat, alt text lengkap, hamburger accessible
- [ ] Kontras teks lolos WCAG AA

---

## 10. Format Jawaban Akhir dari AI

1. Hasilkan file `index.html` lengkap (bukan potongan).
2. Setelah kode, tulis **"Catatan Asumsi"** (maks 5 poin): keputusan yang diambil di luar spec.
3. Tulis **"Cara Mengganti Aset"** (maks 3 poin): di mana mengganti karakter & thumbnail.
4. Jangan menambah fitur/section di luar spec ini kecuali diminta.
