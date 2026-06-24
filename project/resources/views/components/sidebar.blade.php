@php
    $user = auth()->user();
    $menus = [
        ['label' => 'Dasbor', 'route' => 'dashboard', 'icon' => 'layout-dashboard', 'permission' => 'dashboard.view'],
        ['label' => 'Pemesanan', 'route' => 'bookings.index', 'icon' => 'calendar-check', 'permission' => 'bookings.view'],
        ['label' => 'Manajemen Aset', 'route' => 'assets.index', 'icon' => 'camera', 'permission' => 'assets.view'],
        ['label' => 'Pelanggan', 'route' => 'customers.index', 'icon' => 'users', 'permission' => 'customers.view'],
        ['label' => 'Kalender Operasional', 'route' => 'calendar.index', 'icon' => 'calendar-days', 'permission' => 'calendar.view'],
        ['label' => 'Tagihan', 'route' => 'invoices.index', 'icon' => 'file-text', 'permission' => 'invoices.view'],
        ['label' => 'Perawatan', 'route' => 'maintenance.index', 'icon' => 'wrench', 'permission' => 'maintenance.view'],
        ['label' => 'Inventori', 'route' => 'inventory.index', 'icon' => 'package-check', 'permission' => 'inventory.view'],
        ['label' => 'Laporan', 'route' => 'reports.index', 'icon' => 'bar-chart-3', 'permission' => 'reports.view'],
        ['label' => 'Staf', 'route' => 'staff.index', 'icon' => 'id-card', 'permission' => 'users.manage'],
        ['label' => 'Lokasi', 'route' => 'locations.index', 'icon' => 'map-pin', 'permission' => 'assets.create'],
        ['label' => 'Pengaturan', 'route' => 'settings.index', 'icon' => 'settings', 'permission' => 'settings.manage'],
    ];
    $visibleMenus = collect($menus)->filter(fn ($menu) => $user?->hasPermission($menu['permission']) ?? false);
@endphp

<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/40 lg:hidden"
    @click="sidebarOpen = false"></div>

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition duration-200 lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen }">
    <div class="flex h-20 items-center gap-3 border-b border-slate-200 px-5">
        <div
            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 ring-1 ring-blue-500/20">
            <x-application-logo class="h-7 w-7 text-white" />
        </div>
        <div>
            <p class="text-lg font-bold leading-tight text-slate-950">Smart Rental Pro</p>
            <p class="text-xs font-medium text-slate-500">Manajemen Rental Peralatan</p>
        </div>
        <button type="button" class="ml-auto rounded-xl p-2 text-slate-500 hover:bg-slate-100 lg:hidden"
            @click="sidebarOpen = false" aria-label="Tutup sidebar">
            <i data-lucide="x" class="h-5 w-5"></i>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @foreach ($visibleMenus as $menu)
            @php
                $active =
                    request()->routeIs($menu['route']) ||
                    collect($menu['children'] ?? [])->contains(fn($child) => request()->routeIs($child['route']));
            @endphp
            <div x-data="{ open: {{ $active ? 'true' : 'false' }} }">
                <a href="{{ route($menu['route']) }}"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-blue-600 text-white shadow-sm shadow-blue-200' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' }}">
                    <i data-lucide="{{ $menu['icon'] }}" class="h-5 w-5 shrink-0"></i>
                    <span class="min-w-0 flex-1 truncate">{{ $menu['label'] }}</span>
                    @if (!empty($menu['children']))
                        <button type="button" class="rounded-lg p-1" @click.prevent="open = !open"
                            aria-label="Buka submenu {{ $menu['label'] }}">
                            <i data-lucide="chevron-down" class="h-4 w-4 transition"
                                :class="{ 'rotate-180': open }"></i>
                        </button>
                    @endif
                </a>

                @if (!empty($menu['children']))
                    <div x-show="open" class="mt-1 space-y-1 pl-10">
                        @foreach ($menu['children'] as $child)
                            <a href="{{ route($child['route'], $child['params'] ?? []) }}"
                                class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($child['route']) ? 'bg-blue-50 text-blue-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </nav>
</aside>
