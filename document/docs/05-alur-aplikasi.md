# 05 — Alur Aplikasi

## Login

User login, validasi Auth Laravel, cek `is_active`, simpan `last_login_at`, redirect sesuai role.

## Tambah Aset

Admin klik Tambah Aset, isi data, upload foto, simpan, aset berstatus Tersedia, activity log tercatat.

## Booking

1. Pilih customer.
2. Pilih aset.
3. Pilih pickup dan return.
4. Cek ketersediaan.
5. Sistem cek overlap.
6. Tambah asuransi, delivery, diskon.
7. Backend hitung total.
8. Simpan draft atau pending.
9. Admin approve.
10. Aset terblokir sebagai reserved.
11. Saat pickup, booking active dan aset rented.
12. Saat return, booking completed dan aset available/maintenance.

## Invoice

Buat invoice dari booking, sistem menyalin item booking, invoice draft, kirim invoice, finance input pembayaran, status otomatis paid/partially_paid/overdue.

## Maintenance

Buat work order, aset menjadi maintenance, teknisi isi checklist/service log, tandai selesai, aset kembali available jika layak.
