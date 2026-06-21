<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="flex h-20 items-center justify-between px-4 sm:px-6 lg:px-8">
        <input type="search" placeholder="Cari aset, booking, customer..." class="w-full max-w-xl rounded-2xl border border-slate-200 py-3 px-4 text-sm focus:border-blue-500 focus:ring-blue-100">
        <div class="ml-4 flex items-center gap-4">
            <button class="relative rounded-xl p-2">🔔<span class="absolute -right-1 -top-1 rounded-full bg-rose-600 px-1.5 text-[10px] text-white">6</span></button>
            <button class="relative rounded-xl p-2">💬<span class="absolute -right-1 -top-1 rounded-full bg-rose-600 px-1.5 text-[10px] text-white">2</span></button>
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                <div class="hidden text-sm sm:block">
                    <p class="font-semibold text-slate-900">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-500">Pengguna Aktif</p>
                </div>
            </div>
        </div>
    </div>
</header>
