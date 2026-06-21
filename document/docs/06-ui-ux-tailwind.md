# 06 — UI/UX Tailwind

## Gaya Visual

- Modern SaaS admin.
- Light theme.
- Sidebar kiri fixed desktop.
- Topbar sticky.
- Card `rounded-2xl`.
- Border `border-slate-200`.
- Shadow `shadow-sm`.
- Primary blue `#2563EB`.
- Body `#F8FAFC`.
- Text utama `#0F172A`.
- Text sekunder `#64748B`.

## Komponen Dasar

```html
<button class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
  Simpan
</button>

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  Konten Card
</div>

<span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
  Tersedia
</span>
```

## Responsive

| Ukuran | Layout |
|---|---|
| Mobile | Sidebar offcanvas, card 1 kolom, drawer full screen |
| Tablet | Grid 2 kolom |
| Desktop | Sidebar 280px, grid 3–4 kolom, drawer kanan |
