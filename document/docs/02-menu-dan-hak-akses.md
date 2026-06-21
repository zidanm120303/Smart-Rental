# 02 — Menu, Role, dan Hak Akses

## Menu Sidebar Bahasa Indonesia

1. Dashboard
2. Booking
   - Semua Booking
   - Booking Baru
   - Booking Aktif
   - Booking Terlambat
   - Cek Ketersediaan
3. Manajemen Aset
   - Semua Aset
   - Tampilan Tabel
   - Tampilan Grid
   - Kategori Aset
   - Brand
   - Paket/Bundel Aset
   - QR/Barcode
4. Customer
5. Kalender Operasional
6. Invoice
7. Maintenance
8. Inventory
9. Laporan
10. Staff
11. Lokasi
12. Settings

## Permission

| Permission | Fungsi |
|---|---|
| dashboard.view | Melihat dashboard |
| assets.view/create/update/delete | Mengelola aset |
| bookings.view/create/update/cancel/approve | Mengelola booking |
| customers.view/manage | Mengelola customer |
| calendar.view | Melihat kalender |
| invoices.view/manage | Mengelola invoice |
| payments.manage | Mencatat pembayaran |
| maintenance.view/manage | Mengelola maintenance |
| inventory.view/manage | Mengelola inventory |
| reports.view | Melihat laporan |
| users.manage | Mengelola user |
| settings.manage | Mengelola settings |
| activity.view | Melihat log aktivitas |

## Mapping Role

| Modul | Pemilik | Admin | Gudang | Teknisi | Finance |
|---|---:|---:|---:|---:|---:|
| Dashboard | Ya | Ya | Terbatas | Terbatas | Ya |
| Aset | CRUD | CRUD | Update status | Lihat | Lihat |
| Booking | CRUD+approve | CRUD+approve | Pickup/return | Lihat | Lihat |
| Customer | CRUD | CRUD | Lihat | Lihat | Lihat |
| Kalender | Ya | Ya | Ya | Ya | Ya |
| Invoice | Ya | Buat/Lihat | Lihat | Tidak | CRUD |
| Payment | Ya | Lihat | Tidak | Tidak | CRUD |
| Maintenance | Ya | CRUD | Lapor rusak | CRUD | Lihat biaya |
| Inventory | Ya | CRUD | Mutasi | Lihat | Lihat |
| Laporan | Ya | Operasional | Terbatas | Terbatas | Finance |
| Settings | Ya | Terbatas | Tidak | Tidak | Tidak |
