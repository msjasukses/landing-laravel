# Landing Page Aplikasi (Laravel)

Portal daftar aplikasi bergaya *Digital Learning Management System* dengan panel admin
untuk mengatur seluruh isi halaman. Versi Laravel dari konsep di folder `../landing`
(PHP murni + SQLite), sekarang memakai **Laravel 13 + MySQL**.

## Kebutuhan

- Laragon (PHP 8.3+, MySQL 8, Composer)
- Ekstensi PHP: `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`

## Instalasi

Di komputer ini semuanya **sudah terpasang** (database `landing_page` sudah dibuat dan diisi).
Untuk memasang ulang di komputer lain:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Lalu buat database dengan salah satu cara:

1. **Migrasi + seeder** (disarankan): buat database kosong `landing_page`, lalu
   ```bash
   php artisan migrate --seed
   ```
2. **Impor SQL**: impor `database/landing_page.sql` lewat HeidiSQL / phpMyAdmin
   (file ini sudah berisi `CREATE DATABASE landing_page`).

Untuk mereset ke data contoh kapan saja: `php artisan migrate:fresh --seed`.

## Menjalankan

- **Laragon**: klik *Menu → Apache/Nginx → Reload* (atau restart Laragon) agar virtual host
  otomatis dibuat, lalu buka <http://landing-page.test>
- **Tanpa Laragon**: `php artisan serve` lalu buka <http://127.0.0.1:8000>

| Halaman | URL |
|---------|-----|
| Landing page | `/` |
| Panel pengaturan | `/admin` |

**Login default:** `admin` / `admin123` &mdash; segera ganti lewat menu **Akun Admin**.

## Menu pengaturan

| Menu | Isi |
|------|-----|
| **Dashboard** | Ringkasan jumlah aplikasi, grup, fitur, dan pintasan |
| **Pengaturan Umum** | Nama situs, meta description, favicon, judul/sub judul hero, gambar hero + kegelapan overlay, label seksi, warna aksen & warna utama, kotak pencarian, teks footer, kontak |
| **Fitur Header** | Item pada bar putih di bawah hero (ikon, judul, keterangan) |
| **Grup Aplikasi** | Instansi/sekolah: nama, tagline, logo |
| **Aplikasi** | Kartu aplikasi: grup, nama, deskripsi, URL, ikon (50 pilihan), warna, buka di tab baru |
| **Akun Admin** | Nama tampilan, username, ganti password |

Setiap daftar punya tombol naik/turun (urutan tampil), tombol status aktif/nonaktif,
serta tombol ubah dan hapus.

## Database

| Tabel | Isi |
|-------|-----|
| `settings` | Pengaturan situs (key &rarr; value) |
| `features` | Fitur header di bawah hero |
| `app_groups` | Grup / instansi |
| `applications` | Kartu aplikasi (relasi ke `app_groups`, ikut terhapus bila grup dihapus) |
| `users` | Akun admin (login memakai `username`) |

Data contoh (seeder di `database/seeders/`): 3 fitur header, grup **DEMO** berisi
DATA CENTER, PRESENSI, JURNAL, CBT, dan grup **LAYANAN SEKOLAH** berisi KESISWAAN,
BIMBINGAN KONSELING, KURIKULUM, E-RAPOR, serta PPDB ONLINE (contoh aplikasi nonaktif).

## Struktur penting

```
app/
├── Http/Controllers/
│   ├── LandingController.php        landing page publik
│   └── Admin/                       panel pengaturan (wajib login)
│       ├── AuthController.php       masuk / keluar
│       ├── DashboardController.php
│       ├── SettingController.php    pengaturan umum + unggah gambar
│       ├── FeatureController.php    fitur header
│       ├── AppGroupController.php   grup aplikasi
│       ├── ApplicationController.php
│       └── AccountController.php
├── Models/                          Setting, Feature, AppGroup, Application, User
│   └── Concerns/Sortable.php        urutan tampil, naik/turun, aktif/nonaktif
├── Support/Icon.php                 ikon SVG + daftar warna
└── helpers.php                      setting(), upload_url(), asset_v(), initials()
database/
├── migrations/                      skema tabel
├── seeders/                         data contoh
└── landing_page.sql                 dump MySQL siap impor
resources/views/
├── landing.blade.php                tampilan publik
├── layouts/admin.blade.php          sidebar + topbar panel
├── components/icon.blade.php        <x-icon name="lock" size="20" />
└── admin/                           halaman-halaman panel
public/
├── css/site.css, css/admin.css
├── js/site.js (pencarian), js/admin.js (konfirmasi hapus)
└── uploads/                         gambar hero, favicon, logo
```

## Pengujian

```bash
php artisan test
```

## Catatan keamanan

- Semua form memakai token CSRF Laravel; password di-hash otomatis (bcrypt).
- Login dibatasi 10 percobaan per menit.
- Unggahan dibatasi 4 MB dan hanya format gambar (JPG, PNG, WEBP, GIF, SVG, ICO).
- Saat dipasang di hosting: set `APP_ENV=production` dan `APP_DEBUG=false` di `.env`.
