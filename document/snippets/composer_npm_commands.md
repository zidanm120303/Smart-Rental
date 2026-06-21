# Perintah Setup

```bash
composer create-project laravel/laravel smart-rental-pro "10.*"
cd smart-rental-pro

composer require laravel/breeze --dev
php artisan breeze:install blade

npm install
npm install chart.js @fullcalendar/core @fullcalendar/daygrid @fullcalendar/timegrid @fullcalendar/interaction flatpickr
npm run dev

php artisan storage:link
php artisan migrate
php artisan db:seed --class=SmartRentalSeeder
php artisan serve
```

Opsional:

```bash
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
```
