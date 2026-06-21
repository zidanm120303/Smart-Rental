<nav class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="text-sm font-bold text-slate-950">Smart Rental Pro</a>
        <div class="flex items-center gap-4 text-sm font-semibold text-slate-600">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-700">Dasbor</a>
            <a href="{{ route('profile.edit') }}" class="hover:text-blue-700">Profil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="hover:text-blue-700">Keluar</button>
            </form>
        </div>
    </div>
</nav>
