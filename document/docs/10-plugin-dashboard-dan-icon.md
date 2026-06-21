# 10 — Rekomendasi Plugin Dashboard, Icon, dan Asset

## Stack Frontend yang Disarankan

| Kebutuhan | Rekomendasi | Alasan |
|---|---|---|
| CSS utility | Tailwind CSS 3.x | Ringan, fleksibel, cocok untuk Laravel Blade |
| UI component | Alpine.js | Interaksi ringan tanpa SPA penuh |
| Chart dashboard | ApexCharts | Area chart, donut, radial/gauge, bar, mudah dipakai di Blade |
| Kalender operasional | FullCalendar | Month/week/day, drag event, event color, cocok untuk booking dan maintenance |
| Data table | TanStack Table atau table Blade custom | Untuk Laravel Blade, table custom lebih aman; TanStack jika butuh sorting/filter client-side kompleks |
| Date/time picker | Flatpickr | Ringan, mudah, mendukung tanggal dan jam |
| PDF invoice | barryvdh/laravel-dompdf | Generate invoice PDF server-side |
| QR/Barcode | simplesoftwareio/simple-qrcode + picqer/php-barcode-generator | Asset label, invoice, dan scan aset |
| Toast/alert | SweetAlert2 atau Notyf | Feedback user lebih jelas |
| Upload image | FilePond atau input custom Tailwind | FilePond jika butuh preview dan validasi ukuran |

## Plugin Visual Data yang Paling Mendekati Mockup

**ApexCharts** paling mendekati mockup karena mendukung:

- Area/line chart untuk tren pendapatan.
- Donut chart untuk status booking/customer/payment.
- Radial bar/gauge untuk utilisasi aset.
- Bar chart untuk top customer segment.
- Mudah dikustom warna sesuai Tailwind.
- Bisa berjalan dengan Blade + Vite tanpa framework SPA.

**FullCalendar** wajib untuk halaman Kalender Operasional karena kebutuhan kalender tidak ideal dibuat manual dari nol.

## Analisis Icon Pack

Mayoritas icon tersedia di icon pack umum:

| Menu/Fitur | Icon umum tersedia? | Rekomendasi |
|---|---:|---|
| Dashboard | Ya | Heroicons `home`, Font Awesome `fa-house` |
| Booking | Ya | Heroicons `calendar-days`, FA `fa-calendar-check` |
| Aset | Ya | Heroicons `camera`, FA `fa-camera` |
| Customer | Ya | Heroicons `users`, FA `fa-users` |
| Kalender | Ya | Heroicons `calendar`, FA `fa-calendar` |
| Invoice | Ya | Heroicons `document-text`, FA `fa-file-invoice` |
| Maintenance | Ya | Heroicons `wrench-screwdriver`, FA `fa-screwdriver-wrench` |
| Inventory | Ya | Heroicons `archive-box`, FA `fa-boxes-stacked` |
| Staff | Ya | Heroicons `identification`, FA `fa-id-card` |
| Lokasi | Ya | Heroicons `map-pin`, FA `fa-location-dot` |
| Pengaturan | Ya | Heroicons `cog-6-tooth`, FA `fa-gear` |
| Kit peralatan custom | Sebagian | Gunakan SVG custom di `assets/icons/` |
| Empty state rental | Tidak spesifik | Gunakan SVG custom di `assets/illustrations/` |

## Aturan Asset

- Gunakan SVG untuk icon custom agar ringan dan tajam di semua resolusi.
- Simpan semua ilustrasi non-icon di `public/assets/illustrations`.
- Simpan gambar mockup referensi di `public/assets/mockups` atau `docs/reference`.
- Hindari hotlink gambar dari internet.
