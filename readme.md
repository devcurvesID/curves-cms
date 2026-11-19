<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## Instalasi Curves di komputer lokal

Panduan ini digunakan untuk menjalankan CMS Curves di Windows, macOS, atau Linux. Project menggunakan Laravel 9 dan PostgreSQL. Jalankan perintah dari terminal di folder utama project, kecuali disebutkan lain.

### 1. Siapkan kebutuhan aplikasi

Install Git, PHP, Composer, dan PostgreSQL. pgAdmin dapat digunakan untuk membuat dan me-restore database melalui antarmuka grafis.

- PHP minimal 8.1 sesuai `composer.json`. Lingkungan lokal project menggunakan PHP 8.2.
- Composer untuk menginstal dependency sesuai `composer.lock`.
- PostgreSQL untuk database lokal.
- Ekstensi PHP `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `dom`, `xml`, `zip`, dan `gd`. Composer akan memeriksa kebutuhan ekstensi tambahan dari dependency.

Periksa instalasi melalui terminal. Pada Windows, tambahkan lokasi executable ke `PATH` agar perintah tersedia.

```bash
git --version
php -v
composer --version
psql --version
php -m
```

### 2. Clone repository

Ganti `<URL_REPOSITORY>` dan `<NAMA_BRANCH>` dengan URL repository dan branch yang akan digunakan. Gunakan akun Git yang memiliki akses ke repository.

```bash
git clone <URL_REPOSITORY> curves
cd curves
git checkout <NAMA_BRANCH>
```

### 3. Buat konfigurasi lokal

Buat file `.env` di folder utama project menggunakan editor. Repository yang diperiksa belum menyertakan `.env.example`. Isi konfigurasi awal berikut, lalu sesuaikan username dan password PostgreSQL dengan instalasi lokal.

```dotenv
APP_NAME=Curves
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=curves_local
DB_USERNAME=postgres
DB_PASSWORD=password_postgres_lokal

SESSION_DRIVER=file
SESSION_SECURE_COOKIE=false
CACHE_DRIVER=file
QUEUE_DRIVER=sync
BROADCAST_DRIVER=log
FILESYSTEM_DRIVER=local

MAIL_MAILER=log
MAIL_DRIVER=log

CURVES_BACKEND_API_ENABLED=false
CURVES_BACKEND_API_URL=
```

Konfigurasi ini memakai database lokal dan menonaktifkan request keluar ke mobile-curves, termasuk EZYPAY. Simpan `.env` sebagai konfigurasi pribadi komputer Anda. Gunakan backup database development yang disediakan pengelola project untuk langkah restore berikutnya.

### 4. Install dependency PHP

```bash
composer install --prefer-dist
composer check-platform-reqs
php artisan key:generate
```

`key:generate` mengisi `APP_KEY` untuk instalasi lokal. Jika Composer melaporkan ekstensi yang belum tersedia, aktifkan ekstensi tersebut pada konfigurasi PHP CLI, lalu ulangi instalasi dependency.

### 5. Buat database PostgreSQL

Pastikan layanan PostgreSQL aktif. Melalui Query Tool di pgAdmin atau melalui `psql`, jalankan SQL berikut pada koneksi database `postgres`.

```sql
CREATE DATABASE curves_local;
```

Pastikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env` cocok dengan database dan akun PostgreSQL lokal.

### 6. Restore database project

Gunakan backup development yang berisi struktur, data master, view, akun login, dan catatan migrasi. Aplikasi menggunakan view seperti `usermobilecurves_v` serta data master klub, metode pembayaran, dan akun akuntansi.

Untuk backup SQL biasa, jalankan perintah berikut dan sesuaikan lokasi file.

```bash
psql -h 127.0.0.1 -U postgres -d curves_local -f backup.sql
```

Untuk backup PostgreSQL berformat custom, gunakan perintah berikut.

```bash
pg_restore --no-owner --no-privileges -h 127.0.0.1 -U postgres -d curves_local backup.dump
```

Melalui pgAdmin, pilih database `curves_local`, buka menu **Restore**, lalu pilih backup berformat custom. File SQL biasa dapat dijalankan melalui Query Tool. Tunggu restore selesai dan periksa pesan error sebelum melanjutkan.

### 7. Jalankan migrasi terbaru

Setelah database selesai direstore, jalankan migrasi yang belum tercakup dalam backup.

```bash
php artisan optimize:clear
php artisan migrate
php artisan migrate:status
```

Pastikan migrasi terbaru berstatus `Ran`, termasuk migrasi kolom checkout cicilan dan konteks renewal EZYPAY. Jika muncul error kolom yang belum tersedia, periksa status migrasi dan pastikan aplikasi terhubung ke `curves_local`.

### 8. Siapkan penyimpanan file

```bash
php artisan storage:link
```

Pastikan proses PHP memiliki izin menulis ke `storage` dan `bootstrap/cache`. Pada Windows, pembuatan symbolic link dapat memerlukan Developer Mode atau terminal dengan hak administrator.

Untuk menampilkan foto anggota, gambar produk, atau struk lama, salin file upload yang diperlukan dari lingkungan development ke folder penyimpanan yang sesuai. Database menyimpan referensi file tersebut.

### 9. Jalankan aplikasi dan login

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Buka [Dashboard Curves lokal](http://127.0.0.1:8000/cf-lite/dashboard). Login menggunakan akun development yang tersedia pada database hasil restore. Terminal server harus tetap berjalan selama aplikasi digunakan.

### 10. Lihat log saat terjadi error

Pada macOS atau Linux, buka terminal lain di folder project.

```bash
tail -n 100 -F storage/logs/laravel.log
```

Pada Windows PowerShell, gunakan perintah berikut.

```powershell
Get-Content storage/logs/laravel.log -Tail 100 -Wait
```

Jika login terus kembali ke halaman login, periksa `APP_URL`, izin folder session, dan `SESSION_SECURE_COOKIE=false` untuk akses HTTP lokal. Setelah mengubah `.env` saat development, muat ulang konfigurasi dengan perintah berikut.

```bash
php artisan optimize:clear
```

### Build aset frontend

Saat perlu membangun ulang JavaScript atau CSS, periksa kompatibilitas Node.js dengan dependency project terlebih dahulu. `package.json` masih memakai Laravel Mix 1 dan dependency frontend lama. Alur build pada instalasi baru belum diverifikasi.

Perintah build yang tersedia di repository adalah sebagai berikut.

```bash
npm install
npm run dev
```

### Mengaktifkan mobile-curves dan EZYPAY untuk pengujian

Untuk pengujian integrasi, isi URL server mobile-curves development atau sandbox dan aktifkan konfigurasi berikut pada `.env`.

```dotenv
CURVES_BACKEND_API_ENABLED=true
CURVES_BACKEND_API_URL=https://alamat-server-mobile-curves-development
```

Kemudian jalankan `php artisan optimize:clear`. Saat integrasi aktif, aplikasi mengirim request ke server yang dikonfigurasi. Saat konfigurasi bernilai `false`, sinkronisasi dilewati dan pilihan EZYPAY difilter dari dropdown pembayaran. Sinkronisasi yang dilewati perlu dijalankan ulang jika datanya ingin dikirim kemudian.

Panduan ini disusun dari konfigurasi repository. Proses clone, restore, dan login pada komputer baru perlu diverifikasi menggunakan lingkungan lokal masing-masing.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb combination of simplicity, elegance, and innovation give you tools you need to build any application with which you are tasked.

## Learning Laravel

Laravel has the most extensive and thorough documentation and video tutorial library of any modern web application framework. The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it a breeze to get started learning the framework.

If you're not in the mood to read, [Laracasts](https://laracasts.com) contains over 900 video tutorials on a range of topics including Laravel, modern PHP, unit testing, JavaScript, and more. Boost the skill level of yourself and your entire team by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for helping fund on-going Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](http://patreon.com/taylorotwell):

- **[Vehikl](http://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[British Software Development](https://www.britishsoftware.co)**
- **[Styde](https://styde.net)**
- [Fragrantica](https://www.fragrantica.com)
- [SOFTonSOFA](https://softonsofa.com/)

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
