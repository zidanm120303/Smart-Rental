# 12 — Strategi Generate Project via Codex

Tujuan dokumen ini adalah memastikan project Laravel 10 dapat dibuat bertahap tetapi tetap bisa dimulai dari satu prompt master.

## Prinsip Eksekusi Aman

1. Jangan generate semua file secara acak tanpa struktur.
2. Mulai dari database migration, model, seeder, dan policy.
3. Lanjutkan controller + service layer.
4. Lanjutkan Blade layout + komponen UI.
5. Terakhir isi halaman fitur dan JavaScript chart/calendar.
6. Semua query booking wajib melalui `BookingService` agar anti double booking.
7. Semua perubahan stok, pembayaran, return, dan maintenance wajib memakai `DB::transaction()`.
8. Validasi memakai Form Request.
9. Teks UI Bahasa Indonesia.
10. Database MySQL, bukan SQLite.

## Urutan Generate yang Direkomendasikan

1. Auth Laravel Breeze Blade.
2. Migration semua tabel.
3. Model dan relasi.
4. Seeder role, permission, kategori, lokasi, aset contoh, customer contoh.
5. Middleware role/permission sederhana.
6. Dashboard.
7. Manajemen Aset table + grid + detail drawer.
8. Booking wizard.
9. Customer.
10. Kalender Operasional.
11. Invoice + PDF.
12. Maintenance.
13. Inventory.
14. Staff, Lokasi, Pengaturan.
15. Testing manual dan feature test.

## Kesalahan yang Harus Dicegah Codex

- Jangan memakai SQLite.
- Jangan membuat menu/teks Inggris pada UI.
- Jangan menyimpan harga sebagai integer jika ada pajak/decimal.
- Jangan membuat booking tanpa validasi ketersediaan final.
- Jangan menghapus data master yang masih dipakai transaksi.
- Jangan membuat invoice ganda dari booking yang sama.
- Jangan memakai CDN wajib untuk asset inti produksi; gunakan Vite/NPM jika memungkinkan.
- Jangan menulis route yang konflik antara Bahasa Indonesia dan resource name.
