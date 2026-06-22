<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="flex h-20 items-center gap-3 px-4 sm:px-6 lg:px-8">
        <button type="button" class="rounded-xl border border-slate-200 p-2 text-slate-600 lg:hidden"
            @click="sidebarOpen = true" aria-label="Buka sidebar">
            <i data-lucide="menu" class="h-5 w-5"></i>
        </button>

        <form class="hidden w-full max-w-2xl sm:block">
            <label class="relative block">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i data-lucide="search" class="h-4 w-4"></i>
                </span>
                <input type="search" placeholder="Cari aset, pemesanan, pelanggan..."
                    class="h-11 w-full rounded-2xl border-slate-200 bg-white pl-10 pr-16 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">
                <span class="pointer-events-none absolute inset-y-0 right-3 hidden items-center text-xs font-semibold text-slate-400 md:flex">Ctrl K</span>
            </label>
        </form>

        <div class="ml-auto flex items-center gap-2 sm:gap-3">
            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-2 py-2 shadow-sm md:min-w-72">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="hidden leading-tight md:block">
                    <p class="text-sm font-bold text-slate-950">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                    <p class="text-xs font-medium text-slate-500">{{ auth()->user()->role_label ?? 'Admin' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-xl p-2 text-slate-500 hover:bg-slate-100" title="Keluar" aria-label="Keluar">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
