# 11 — Standar Bahasa Indonesia, Status, dan Label UI

Semua teks UI wajib Bahasa Indonesia. Jangan memakai label Inggris di menu utama, tombol, validasi, status, placeholder, dan flash message.

## Menu Utama Bahasa Indonesia

| Route | Label |
|---|---|
| /dashboard | Dashboard |
| /booking | Booking |
| /aset | Manajemen Aset |
| /customer | Customer |
| /kalender | Kalender Operasional |
| /invoice | Invoice |
| /maintenance | Maintenance |
| /inventory | Inventory |
| /staff | Staff |
| /lokasi | Lokasi |
| /settings | Pengaturan |

## Status Aset

| Nilai DB | Label UI | Warna |
|---|---|---|
| available | Tersedia | Hijau |
| rented | Disewa | Biru |
| reserved | Dipesan | Kuning |
| maintenance | Maintenance | Ungu/Merah muda |
| retired | Tidak Aktif | Abu-abu |

## Status Booking

| Nilai DB | Label UI |
|---|---|
| draft | Draft |
| pending | Menunggu Persetujuan |
| approved | Disetujui |
| active | Sedang Berjalan |
| completed | Selesai |
| cancelled | Dibatalkan |
| overdue | Terlambat |

## Status Invoice

| Nilai DB | Label UI |
|---|---|
| draft | Draft |
| sent | Terkirim |
| paid | Lunas |
| partially_paid | Dibayar Sebagian |
| overdue | Jatuh Tempo |
| void | Dibatalkan |

## Tombol Umum

- Tambah Data
- Simpan
- Simpan Perubahan
- Hapus
- Edit
- Detail
- Filter
- Reset Filter
- Export
- Import
- Cek Ketersediaan
- Buat Booking
- Buat Invoice
- Catat Pembayaran
- Jadwalkan Maintenance
- Tandai Selesai
- Cetak PDF

## Placeholder Form

- Cari aset, booking, atau customer...
- Masukkan nama customer
- Pilih kategori aset
- Pilih lokasi
- Pilih tanggal pickup
- Pilih tanggal kembali
- Tulis catatan tambahan

## Flash Message

- Data berhasil disimpan.
- Data berhasil diperbarui.
- Data berhasil dihapus.
- Booking berhasil dibuat.
- Aset tidak tersedia pada rentang tanggal tersebut.
- Invoice berhasil dibuat dari booking.
- Pembayaran berhasil dicatat.
- Maintenance berhasil dijadwalkan.
