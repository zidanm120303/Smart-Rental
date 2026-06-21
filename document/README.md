# Smart Rental Pro — Paket Plan Laravel 10

Blueprint ini disiapkan untuk membuat sistem rental peralatan kamera, sound system, lighting, drone, tripod, lensa, mixer, speaker, dan aksesori menggunakan **Laravel 10**, **PHP 8.1**, **MySQL dari FlyEnv**, dan **Tailwind CSS**.

Semua menu, teks tampilan, placeholder, flash message, status, dan validasi UI dirancang menggunakan **Bahasa Indonesia**.

## Isi Paket

- `docs/` — analisis sistem, skema database, alur, spesifikasi halaman, UI/UX, keamanan, setup, dan roadmap.
- `sql/` — SQL schema MySQL dan query penting.
- `snippets/` — potongan kode Laravel 10: migration, model, controller, service, request, blade, css, js, route, seeder.
- `assets/` — icon SVG, ilustrasi empty state, dan mockup referensi.
- `prompts/` — prompt master untuk Codex.
- `checklists/` — checklist coding, testing, dan security.

## Cara Pakai Cepat

1. Buat project Laravel 10.
2. Pastikan `.env` memakai MySQL, bukan SQLite.
3. Jalankan migration/seeder berdasarkan snippet.
4. Gunakan `prompts/MASTER_PROMPT_CODEX.md` untuk membangun project penuh via Codex.
5. Pakai mockup di `assets/mockups/` sebagai referensi visual.


## Catatan Penting

Paket ini adalah **blueprint lengkap + snippet awal**, bukan full project Laravel final yang sudah menjalankan `composer install`. Gunakan `prompts/MASTER_PROMPT_CODEX.md` di Codex untuk membuat project penuh berdasarkan dokumen dan snippet ini.

## Rekomendasi Command Awal

```bash
composer create-project laravel/laravel smart-rental-pro "^10.0"
cd smart-rental-pro
composer require laravel/breeze barryvdh/laravel-dompdf simplesoftwareio/simple-qrcode picqer/php-barcode-generator
php artisan breeze:install blade
npm install apexcharts @fullcalendar/core @fullcalendar/daygrid @fullcalendar/timegrid @fullcalendar/interaction flatpickr alpinejs
npm install
npm run dev
php artisan migrate:fresh --seed
```

Pastikan `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_rental_pro
DB_USERNAME=smart_rental_pro
DB_PASSWORD=sm@rt@dm1n
```
