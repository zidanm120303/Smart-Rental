# Smart Rental Pro

Smart Rental Pro adalah aplikasi manajemen rental peralatan berbasis Laravel untuk mengelola aset, pemesanan, pelanggan, kalender operasional, invoice, pembayaran, maintenance, inventori, staf, lokasi, laporan, dan pengaturan perusahaan.

Repository GitHub:

```bash
https://github.com/zidanm120303/Smart-Rental.git
```

## Struktur Folder

```text
Smart-Rental/
+-- document/     # Dokumentasi, plan, dan catatan pengembangan
+-- project/      # Aplikasi Laravel 10
```

Semua command Laravel, Composer, dan npm dijalankan dari folder `project`.

```bash
cd project
```

## Requirement Versi

Versi yang dipakai saat project dibuat:

| Kebutuhan | Versi |
| --- | --- |
| Laravel | 10.50.2 |
| PHP | 8.1.x, tested 8.1.34 |
| MySQL | 8.0+ atau MySQL FlyEnv, tested MySQL CLI 9.7.1 |
| Composer | 2.x, tested 2.6.6 |
| Node.js | 18.x atau 20.x LTS, tested 18.8.0 |
| npm | 8.x+, tested 8.18.0 |
| Tailwind CSS | 3.1.0 |
| Vite | 4.x |

PHP extension yang perlu aktif:

```text
openssl, pdo, pdo_mysql, mbstring, tokenizer, xml, ctype, fileinfo, gd, curl, zip, intl
```

## Instalasi Software di Windows

Jika laptop teman belum punya Git, Composer, atau Node.js, install lewat PowerShell:

```powershell
winget install --id Git.Git -e
winget install --id Composer.Composer -e
winget install --id OpenJS.NodeJS.LTS -e
```

Untuk PHP dan MySQL, gunakan FlyEnv:

1. Install FlyEnv.
2. Buka FlyEnv.
3. Install PHP 8.1.
4. Install MySQL 8.0+.
5. Aktifkan PHP extension yang dibutuhkan.
6. Pastikan path PHP dan MySQL dari FlyEnv bisa dipakai di terminal, atau jalankan command dari terminal bawaan FlyEnv.

Cek instalasi:

```bash
git --version
php -v
composer --version
node -v
npm -v
mysql --version
```

## Clone Project dari GitHub

```bash
git clone https://github.com/zidanm120303/Smart-Rental.git
cd Smart-Rental/project
```

## Setup Project Pertama Kali

Install dependency backend:

```bash
composer install
```

Install dependency frontend:

```bash
npm install
```

Buat file `.env` dari contoh:

```powershell
copy .env.example .env
```

Untuk Git Bash atau macOS/Linux:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

## Konfigurasi Database FlyEnv MySQL

Buka file `project/.env`, lalu sesuaikan bagian database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_rental_pro
DB_USERNAME=root
DB_PASSWORD=
```

Jika password MySQL di FlyEnv berbeda, isi `DB_PASSWORD` sesuai password lokal masing-masing.

Buat database:

```bash
mysql -u root -p
```

Lalu jalankan SQL:

```sql
CREATE DATABASE smart_rental_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Jika MySQL root tidak memakai password, bisa pakai:

```bash
mysql -u root
```

## Migrasi dan Seeder

Jalankan migration dan isi data awal:

```bash
php artisan migrate:fresh --seed
```

Seeder membuat data awal berurutan untuk kebutuhan demo dan pengujian:

```text
100 aset
100 pelanggan
100 pemesanan
100 invoice
100 maintenance
100 item inventori
```

Buat symbolic link storage:

```bash
php artisan storage:link
```

## Menjalankan Aplikasi

Terminal 1 untuk Laravel:

```bash
php artisan serve
```

Terminal 2 untuk Vite dan Tailwind:

```bash
npm run dev
```

Buka:

```text
http://127.0.0.1:8000
```

Akun demo:

```text
Email: admin@smartrental.local
Password: password
```

## Build Frontend

Untuk membuat asset production:

```bash
npm run build
```

## Test Project

```bash
php artisan test
```

## Command Laravel yang Sering Dipakai

Membersihkan cache:

```bash
php artisan optimize:clear
```

Membuat ulang database dari awal:

```bash
php artisan migrate:fresh --seed
```

Menjalankan migration tanpa menghapus data:

```bash
php artisan migrate
```

Menjalankan seeder saja:

```bash
php artisan db:seed
```

## Alur Kerja Git untuk Tim

### 1. Ambil Update Terbaru

Sebelum mulai kerja, selalu ambil update dari GitHub:

```bash
git checkout main
git pull origin main
```

### 2. Buat Branch Fitur

Jangan langsung kerja di `main`.

```bash
git checkout -b fitur/nama-fitur
```

Contoh:

```bash
git checkout -b fitur/crud-pelanggan
```

### 3. Simpan Perubahan

Cek file yang berubah:

```bash
git status
```

Tambahkan file:

```bash
git add .
```

Commit:

```bash
git commit -m "Tambah CRUD pelanggan"
```

Push branch ke GitHub:

```bash
git push origin fitur/nama-fitur
```

### 4. Merge Update dari Main ke Branch

Jika `main` sudah berubah saat kalian masih mengerjakan fitur:

```bash
git checkout main
git pull origin main
git checkout fitur/nama-fitur
git merge main
```

Jika ada conflict:

1. Buka file yang conflict.
2. Pilih kode yang benar.
3. Hapus tanda `<<<<<<<`, `=======`, dan `>>>>>>>`.
4. Jalankan:

```bash
git add .
git commit
git push origin fitur/nama-fitur
```

### 5. Merge Branch ke Main

Cara paling aman:

1. Buka repository di GitHub.
2. Buat Pull Request dari branch fitur ke `main`.
3. Review perubahan.
4. Klik Merge Pull Request.

Jika harus merge dari terminal:

```bash
git checkout main
git pull origin main
git merge fitur/nama-fitur
git push origin main
```

### 6. Push Langsung ke Main

Gunakan hanya jika disepakati tim.

```bash
git checkout main
git pull --rebase origin main
git add .
git commit -m "Pesan commit"
git push origin main
```

## File yang Tidak Boleh Di-commit

Jangan commit file/folder ini:

```text
project/.env
project/vendor/
project/node_modules/
project/storage/logs/*.log
project/database/*.sqlite
project/public/build/
```

File tersebut sudah masuk `.gitignore`.

## Troubleshooting

Jika halaman error setelah clone:

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan optimize:clear
```

Jika error database:

1. Pastikan MySQL FlyEnv sedang aktif.
2. Pastikan database `smart_rental_pro` sudah dibuat.
3. Pastikan `DB_USERNAME` dan `DB_PASSWORD` di `.env` benar.
4. Jalankan:

```bash
php artisan migrate:fresh --seed
```

Jika asset CSS/JS tidak muncul:

```bash
npm install
npm run dev
```

Jika upload/storage tidak muncul:

```bash
php artisan storage:link
```

## Catatan Pengembangan

Sebelum push:

```bash
php artisan test
npm run build
git status
```

Gunakan pesan commit yang jelas, misalnya:

```bash
git commit -m "Perbaiki validasi invoice"
git commit -m "Tambah halaman inventori"
git commit -m "Rapikan tampilan dashboard"
```
