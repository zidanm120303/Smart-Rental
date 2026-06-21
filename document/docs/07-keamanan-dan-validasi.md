# 07 — Keamanan dan Validasi

## Wajib

- Semua route memakai `auth`.
- Semua form memakai `@csrf`.
- Store/update memakai Form Request.
- Aksi tulis memakai permission/policy.
- Upload divalidasi mime dan size.
- Password memakai `Hash::make`.
- `.env` tidak masuk repository.
- Jangan gunakan SQLite.
- Gunakan `DB::transaction()` untuk booking.
- Availability dicek ulang saat simpan booking.

## Validasi Booking

- `customer_id` wajib.
- `asset_ids` wajib minimal 1.
- `pickup_at` wajib dan tidak boleh kurang dari sekarang.
- `return_at` wajib dan harus setelah pickup.
- Customer blacklist tidak boleh booking kecuali override.
- Aset maintenance/rented/retired tidak boleh dibooking.
- Jadwal overlap ditolak.

## Error Bahasa Indonesia

| Kasus | Pesan |
|---|---|
| Unauthorized | Anda tidak memiliki akses ke fitur ini. |
| Booking bentrok | Aset tidak tersedia pada jadwal yang dipilih. |
| Customer blacklist | Customer ini tidak dapat membuat booking baru. |
| Payment berlebih | Nominal pembayaran melebihi total tagihan. |
| Upload salah | File yang diunggah tidak sesuai format. |
