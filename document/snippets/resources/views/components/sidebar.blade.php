@php
$menus = [
    ['label' => 'Dashboard', 'route' => 'dashboard'],
    ['label' => 'Booking', 'route' => 'bookings.index'],
    ['label' => 'Manajemen Aset', 'route' => 'assets.index'],
    ['label' => 'Customer', 'route' => 'customers.index'],
    ['label' => 'Kalender Operasional', 'route' => 'calendar.index'],
    ['label' => 'Invoice', 'route' => 'invoices.index'],
    ['label' => 'Maintenance', 'route' => 'maintenance.index'],
    ['label' => 'Inventory', 'route' => 'assets.index'],
    ['label' => 'Laporan', 'route' => 'dashboard'],
    ['label' => 'Staff', 'route' => 'dashboard'],
    ['label' => 'Lokasi', 'route' => 'dashboard'],
    ['label' => 'Settings', 'route' => 'settings.index'],
];
@endphp

<aside class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-slate-200 bg-white lg:flex lg:flex-col">
    <div class="flex h-20 items-center gap-3 px-6">
        <div class="grid h-11 w-11 place-items-center rounded-2xl bg-blue-600 text-white shadow-sm">📷</div>
        <div>
            <div class="text-base font-bold text-slate-950">Smart Rental Pro</div>
            <div class="text-xs text-slate-500">Manajemen Rental Peralatan</div>
        </div>
    </div>
    <nav class="flex-1 space-y-1 px-4 py-4">
        @foreach ($menus as $menu)
            @php $active = request()->routeIs($menu['route']) || request()->routeIs(strtok($menu['route'], '.') . '.*'); @endphp
            <a href="{{ route($menu['route']) }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ $active ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                <span>●</span><span>{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
