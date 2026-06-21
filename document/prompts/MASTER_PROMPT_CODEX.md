# MASTER PROMPT CODEX — Smart Rental Pro Laravel 10

Kamu adalah senior Laravel engineer dan UI engineer. Buat project web **Smart Rental Pro** untuk rental peralatan Kamera, Sound System, Lighting, Drone, Tripod, Lensa, Mixer, Speaker, dan Aksesori.

## Stack Wajib

- Laravel 10
- PHP 8.1
- MySQL dari FlyEnv
- Tailwind CSS via Vite
- Laravel Breeze Blade untuk auth
- Alpine.js untuk interaksi ringan
- ApexCharts untuk dashboard chart
- FullCalendar untuk kalender operasional
- DomPDF untuk cetak invoice PDF
- Jangan memakai SQLite
- Semua teks UI, menu, tombol, placeholder, validasi, status, dan flash message wajib Bahasa Indonesia

## Target Fitur

1. Dashboard
   - KPI: total aset, booking aktif, pendapatan bulan ini, aset maintenance.
   - Chart tren pendapatan, status booking, utilisasi aset, top item disewa, alert operasional.
2. Manajemen Aset
   - Mode table dan grid.
   - Filter kategori, status, lokasi, kondisi.
   - Detail drawer aset.
   - Spesifikasi, media, QR/barcode, harga, deposit, lokasi, status.
   - Aksi: tambah, edit, hapus soft delete/nonaktif, cek ketersediaan, booking, maintenance.
3. Booking Wizard
   - Step: Customer, Pilih Item, Jadwal, Delivery/Pickup, Harga, Review.
   - Anti double booking dengan transaksi database.
   - Kalkulasi subtotal, diskon, asuransi, delivery, pajak, deposit, total.
4. Customer
   - CRUD customer personal/perusahaan.
   - Riwayat booking, invoice, dokumen, catatan, tag.
5. Kalender Operasional
   - FullCalendar month/week/day.
   - Event booking, pickup, return, maintenance, transport, staff.
6. Invoice
   - Generate dari booking.
   - Item invoice, subtotal, diskon, pajak, deposit, total due.
   - Status draft/terkirim/lunas/sebagian/jatuh tempo.
   - Catat pembayaran.
   - Cetak PDF.
7. Maintenance
   - Work order maintenance.
   - Checklist inspeksi.
   - Status baru/proses/menunggu sparepart/selesai.
   - Update status aset saat maintenance.
8. Inventory
   - Barang habis pakai: baterai, kabel, gaffer tape, sparepart.
   - Mutasi masuk/keluar/adjustment.
9. Staff dan Role
   - Role: Admin, Operator, Finance, Teknisi, Viewer.
   - Permission berbasis route/action.
10. Lokasi
   - Gudang, studio, rak, shelf.
11. Pengaturan
   - Profil perusahaan, pajak, mata uang, metode pembayaran, aturan booking, notifikasi.

## Struktur Teknis

Gunakan struktur:

- `app/Models`
- `app/Http/Controllers`
- `app/Http/Requests`
- `app/Services`
- `app/Policies`
- `database/migrations`
- `database/seeders`
- `resources/views/layouts`
- `resources/views/components`
- `resources/views/pages/{fitur}`
- `resources/js`
- `resources/css`
- `public/assets/icons`
- `public/assets/illustrations`

## Database

Buat migration MySQL untuk tabel:

- users
- roles
- permissions
- role_user
- permission_role
- locations
- asset_categories
- asset_brands
- assets
- asset_media
- asset_specifications
- asset_kits
- asset_kit_items
- customers
- bookings
- booking_items
- booking_services
- invoices
- invoice_items
- payments
- maintenance_requests
- maintenance_checklists
- inventory_items
- inventory_movements
- activity_logs
- settings

Gunakan foreign key, decimal untuk uang, enum/string status yang konsisten, soft delete untuk data master penting, timestamps.

## Rule Booking Anti Bentrok

Aset dianggap bentrok jika ada booking dengan status pending/approved/active dan rentang waktu overlap:

```php
$overlap = $existingPickup < $requestedReturn && $existingReturn > $requestedPickup;
```

Implementasikan final check di `BookingService::createBooking()` memakai:

- `DB::transaction()`
- `lockForUpdate()` pada aset yang dipilih
- validasi semua item tersedia sebelum insert booking dan booking_items

## UI Wajib

- Sidebar kiri, topbar search, profil user, notifikasi.
- Card putih, border halus, shadow lembut, radius besar.
- Primary color biru/indigo.
- Responsive: desktop grid, tablet collapsed sidebar, mobile drawer + stacked cards.
- Semua label Bahasa Indonesia.
- Gunakan komponen Blade reusable: sidebar, topbar, stat-card, badge, table, modal, drawer.

## Keamanan

- Auth wajib semua fitur.
- Policy/permission untuk akses fitur.
- CSRF semua form.
- Validasi Form Request.
- Sanitasi upload, validasi mime/size.
- Jangan simpan password plaintext.
- Jangan expose file upload langsung tanpa validasi.
- Hindari mass assignment berbahaya; gunakan `$fillable` terkontrol.
- Semua transaksi finansial dan booking memakai DB transaction.

## Output yang Diminta

Buat semua file project Laravel lengkap dan runnable. Setelah membuat file, cek:

- `php artisan migrate:fresh --seed`
- `npm install && npm run build`
- route tidak error
- semua menu tampil Bahasa Indonesia
- dashboard bisa load tanpa data kosong error
- halaman aset, booking, customer, kalender, invoice, maintenance, pengaturan bisa dibuka

Jika ada dependensi yang belum ada, tambahkan pada instruksi instalasi dan composer/npm command.
