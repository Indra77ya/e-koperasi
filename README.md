# Sistem Informasi E-Koperasi

Aplikasi manajemen koperasi yang mencakup pengelolaan anggota, simpanan, pinjaman (dengan perhitungan bunga dan denda), serta akuntansi dasar (jurnal, buku besar, neraca, laba rugi).

## Fitur Utama
- **Manajemen Anggota & Nasabah**: Pengelolaan data identitas anggota koperasi dan nasabah non-anggota.
- **Pinjaman**: Pengajuan, persetujuan, pencairan, dan pembayaran angsuran dengan dukungan berbagai metode perhitungan bunga (Flat, Efektif, Anuitas).
- **Akuntansi**: Chart of Accounts (COA), Jurnal Umum, Buku Besar, Neraca Saldo, Laba Rugi.
- **Laporan**: Laporan arus kas, pinjaman beredar, pinjaman macet, dan pendapatan.
- **Manajemen Kolektibilitas**: Pemantauan status kelancaran pembayaran (Lancar, DPK, Macet).

## Persyaratan Sistem

Sebelum memulai instalasi, pastikan sistem Anda memiliki:
- **PHP**: Versi 7.2 atau lebih baru.
- **Composer**: Manajer dependensi PHP.
- **Database**: MySQL atau MariaDB (Versi 5.7+ direkomendasikan).
- **Web Server**: Apache atau Nginx.

## Panduan Instalasi Langkah-demi-Langkah

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di lingkungan lokal Anda (Localhost).

### 1. Clone atau Unduh Repositori
Buka terminal atau command prompt, lalu clone repositori ini ke folder root web server Anda (misalnya `htdocs` di XAMPP atau `/var/www/html` di Linux).

```bash
git clone <url-repository-ini>
cd nama-folder-project
```

### 2. Instal Dependensi PHP
Jalankan perintah berikut untuk mengunduh semua pustaka yang dibutuhkan oleh aplikasi:

```bash
composer install
```

### 3. Konfigurasi Environment
Salin file konfigurasi contoh `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```
*Jika Anda menggunakan Windows dan tidak memiliki perintah `cp`, Anda bisa menyalin dan menamai ulang file tersebut secara manual melalui File Explorer.*

### 4. Konfigurasi Database
1. Buka aplikasi manajemen database Anda (seperti phpMyAdmin, DBeaver, atau MySQL Workbench).
2. Buat database baru (kosong), misalnya dengan nama `e_koperasi`.
3. Buka file `.env` yang baru saja Anda buat dengan teks editor.
4. Cari bagian konfigurasi database dan sesuaikan dengan pengaturan Anda:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_koperasi      <-- Ganti dengan nama database Anda
DB_USERNAME=root            <-- Ganti dengan username database Anda
DB_PASSWORD=                <-- Ganti dengan password database Anda
```

### 5. Generate Application Key
Generate kunci enkripsi aplikasi Laravel:

```bash
php artisan key:generate
```

### 6. Migrasi dan Seeding Database
Jalankan perintah berikut untuk membuat tabel-tabel di database dan mengisi data awal (data dummy untuk testing):

```bash
php artisan migrate
php artisan db:seed
```
*Langkah ini akan membuat tabel users, roles, permissions, chart of accounts, serta data contoh untuk nasabah dan pinjaman.*

### 7. Link Storage (Penting untuk Upload File)
Agar file yang diunggah (seperti foto KTP atau dokumen jaminan) dapat diakses publik, buat symbolic link dari `storage/app/public` ke `public/storage`:

```bash
php artisan storage:link
```

### 8. Jalankan Aplikasi
Jalankan server pengembangan bawaan Laravel:

```bash
php artisan serve
```
Akses aplikasi melalui browser di alamat: `http://localhost:8000`

## Akun Login Default

Gunakan kredensial berikut untuk masuk sebagai administrator:

- **Email**: `ekoperasi@gmail.com`
- **Password**: `secret`

## Troubleshooting

- **Error Permission/Izin Folder**: Jika Anda menemui error terkait izin tulis (permission denied), pastikan folder `storage` dan `bootstrap/cache` memiliki izin tulis.
  - Linux/Mac: `chmod -R 775 storage bootstrap/cache`
- **Tampilan Rusak/CSS Tidak Load**: Pastikan URL aplikasi di `.env` (`APP_URL`) sesuai dengan alamat akses Anda. Contoh: `APP_URL=http://localhost:8000`.
- **Composer Error**: Jika `composer install` gagal, coba jalankan `composer update` atau pastikan ekstensi PHP yang dibutuhkan (seperti `php-xml`, `php-mbstring`, `php-zip`) sudah aktif.
