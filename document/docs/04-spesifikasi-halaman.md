# 04 — Spesifikasi Halaman

## Dashboard

Komponen: KPI Total Aset, Booking Aktif, Pendapatan Bulan Ini, Aset Maintenance, chart pendapatan, donut status booking, gauge utilisasi, booking terbaru, top rented items, kalender mini, alert, quick action.

## Manajemen Aset

### Table View

Kolom: checkbox, Kode Aset, Nama Aset, Kategori, Lokasi, Status, Kondisi, Tarif Harian, Maintenance Terakhir, Aksi.

Filter: search, kategori, status, lokasi, kondisi, sort.

### Grid View

Card berisi foto aset, nama, kategori, status, lokasi/rak, tarif harian, ringkasan spesifikasi, tombol lihat/kalender/edit/menu.

### Detail Drawer

Foto besar, kode/serial, status, utilisasi, spesifikasi, bundled item, riwayat booking, riwayat maintenance, QR/barcode, tombol Edit Aset, Cek Ketersediaan, Buat Booking, Jadwalkan Maintenance.

## Booking Wizard

Step: Customer, Pilih Aset, Jadwal Pickup/Return, Pengiriman, Harga/Add-on, Review.

Aturan: return setelah pickup, minimal durasi dari settings, aset harus tersedia, harga dihitung backend, booking bisa draft/pending/approved, pickup membuat aset disewa, return membuat aset tersedia/maintenance.

## Customer

KPI customer, tabel customer, search/filter, detail panel, rental history, kategori favorit, dokumen verifikasi, billing profile, notes.

## Kalender Operasional

FullCalendar dengan event Booking, Pickup, Return, Maintenance, Transport, Staff Assignment. Filter tipe event, kategori aset, lokasi, staff, status.

## Invoice

KPI invoice, tabel invoice, preview invoice, payment summary, recent payment, action kirim invoice/catat pembayaran/download PDF/print.

## Maintenance

KPI task, inspeksi, aset unavailable, biaya maintenance, summary Baru/Diproses/Menunggu Part/Selesai, work order table, detail drawer checklist dan service log.

## Settings

Profil perusahaan, aturan rental, kebijakan booking, pajak/mata uang, metode pembayaran, notifikasi, role, kategori aset, lokasi, integrasi, tampilan aplikasi.
