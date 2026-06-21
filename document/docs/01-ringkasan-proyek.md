# 01 — Ringkasan Proyek

## Nama Sistem

**Smart Rental Pro** — sistem manajemen rental aset untuk kamera, sound system, lighting, drone, tripod, lensa, mixer, speaker, dan peralatan event.

## Tujuan

Membuat aplikasi web yang dapat mengelola:

1. Data aset dan kondisi aset.
2. Booking / reservasi aset.
3. Ketersediaan aset agar tidak double booking.
4. Customer dan histori transaksi.
5. Kalender operasional pickup, return, maintenance, dan transport.
6. Invoice dan pembayaran.
7. Maintenance aset.
8. Inventory consumable seperti baterai, kabel, gaffer tape, SD card.
9. Laporan pendapatan, utilisasi aset, dan performa customer.

## Prinsip Sistem

- Database MySQL, bukan SQLite.
- Backend Laravel 10 + PHP 8.1.
- UI menggunakan Blade + Tailwind CSS.
- Semua teks tampilan Bahasa Indonesia.
- Booking wajib aman dari bentrok jadwal.
- Harga booking dihitung di backend.
- Setiap aksi penting masuk activity log.
- Gunakan soft delete untuk data penting.
- Aplikasi bisa dipakai multi-role.

## Role

| Role | Fokus |
|---|---|
| Pemilik | Akses penuh, laporan, settings, role, finance. |
| Admin Operasional | Aset, customer, booking, invoice, kalender. |
| Staff Gudang | Pickup, return, cek kondisi aset. |
| Teknisi | Maintenance, checklist, inspeksi. |
| Finance | Invoice, payment, laporan pembayaran. |

## Library Frontend yang Mendekati Mockup

| Kebutuhan | Rekomendasi | Alasan |
|---|---|---|
| Chart dashboard | Chart.js | Ringan, mudah untuk Blade, line/donut/bar. |
| Kalender | FullCalendar | Cocok untuk jadwal pickup/return/maintenance. |
| Interaksi ringan | Alpine.js | Modal, drawer, dropdown, sidebar mobile. |
| Date picker | Flatpickr | Input pickup-return rapi dan ringan. |
| Toast | Notyf/Toastify | Feedback simpan/error. |
| PDF opsional | DomPDF | Cetak invoice PDF dari Blade. |
| Export opsional | Laravel Excel | Export laporan Excel. |
