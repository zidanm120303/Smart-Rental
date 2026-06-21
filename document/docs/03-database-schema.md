# 03 — Skema Database MySQL

## Tabel Inti

1. users
2. roles
3. permissions
4. role_user
5. permission_role
6. locations
7. asset_categories
8. asset_brands
9. assets
10. asset_media
11. asset_specifications
12. asset_kits
13. asset_kit_items
14. customers
15. bookings
16. booking_items
17. booking_services
18. invoices
19. invoice_items
20. payments
21. maintenance_requests
22. maintenance_checklists
23. inventory_items
24. inventory_movements
25. activity_logs
26. settings

## ERD Mermaid

```mermaid
erDiagram
    users }o--o{ roles : role_user
    roles }o--o{ permissions : permission_role
    locations ||--o{ assets : menyimpan
    asset_categories ||--o{ assets : kategori
    asset_brands ||--o{ assets : brand
    assets ||--o{ asset_media : foto
    assets ||--o{ asset_specifications : spesifikasi
    assets ||--o{ booking_items : disewa
    customers ||--o{ bookings : membuat
    users ||--o{ bookings : input
    bookings ||--o{ booking_items : item
    bookings ||--o{ booking_services : add_on
    bookings ||--o| invoices : invoice
    invoices ||--o{ invoice_items : detail
    invoices ||--o{ payments : pembayaran
    assets ||--o{ maintenance_requests : maintenance
    maintenance_requests ||--o{ maintenance_checklists : checklist
    inventory_items ||--o{ inventory_movements : mutasi
```

## Field Penting: assets

| Field | Tipe | Keterangan |
|---|---|---|
| asset_code | string unique | Kode aset, contoh AST-CAM-0001 |
| category_id | foreignId | Kategori |
| brand_id | foreignId nullable | Brand |
| location_id | foreignId | Lokasi |
| name | string | Nama aset |
| serial_number | string nullable | Serial number |
| daily_rate | decimal | Tarif harian |
| deposit_amount | decimal | Deposit |
| replacement_value | decimal nullable | Nilai penggantian |
| condition_status | string | excellent, good, fair, damaged |
| availability_status | string | available, rented, reserved, maintenance, retired |
| shelf_position | string nullable | Rak/posisi |
| qr_code | string nullable | Path QR |
| barcode | string nullable | Barcode |
| is_active | boolean | Status aktif |

## Field Penting: bookings

| Field | Tipe | Keterangan |
|---|---|---|
| booking_code | string unique | Kode booking |
| customer_id | foreignId | Customer |
| user_id | foreignId | Pembuat |
| pickup_at | datetime | Jadwal pickup |
| return_at | datetime | Jadwal kembali |
| delivery_method | string | pickup/delivery |
| status | string | draft, pending, approved, active, completed, cancelled, overdue |
| subtotal, discount_amount, insurance_amount, delivery_fee, tax_amount, deposit_amount, grand_total | decimal | Kalkulasi biaya |

## Rule Anti Double Booking

```sql
existing.pickup_at < requested_return_at
AND existing.return_at > requested_pickup_at
```

Final check wajib dilakukan ulang di `BookingService::createBooking()` memakai `DB::transaction()` dan `lockForUpdate()`.
