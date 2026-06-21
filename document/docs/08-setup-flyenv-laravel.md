# 08 — Setup Laravel 10 + FlyEnv MySQL

## Buat Project

```bash
composer create-project laravel/laravel smart-rental-pro "10.*"
cd smart-rental-pro
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm install chart.js @fullcalendar/core @fullcalendar/daygrid @fullcalendar/timegrid @fullcalendar/interaction flatpickr
npm run dev
```

## `.env`

```env
APP_NAME="Smart Rental Pro"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_rental_pro
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

## SQL Database

```sql
CREATE DATABASE smart_rental_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Jalankan

```bash
php artisan storage:link
php artisan migrate
php artisan db:seed --class=SmartRentalSeeder
npm run dev
php artisan serve
```

## Akun Default

| Role | Email | Password |
|---|---|---|
| Pemilik | pemilik@smartrental.local | password |
| Admin | admin@smartrental.local | password |
| Staff Gudang | gudang@smartrental.local | password |
| Teknisi | teknisi@smartrental.local | password |
| Finance | finance@smartrental.local | password |
