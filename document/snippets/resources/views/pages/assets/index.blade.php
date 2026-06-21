<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-950">Manajemen Aset</h1>
                <p class="text-sm text-slate-500">Kelola aset rental, status ketersediaan, lokasi, kondisi, dan tarif.</p>
            </div>
            <a href="{{ route('assets.create') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">+ Tambah Aset</a>
        </div>

        <form method="GET" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 md:grid-cols-5">
                <input name="q" value="{{ request('q') }}" placeholder="Cari nama aset atau kode aset..." class="rounded-xl border-slate-200 text-sm md:col-span-2">
                <select name="status" class="rounded-xl border-slate-200 text-sm">
                    <option value="">Semua Status</option>
                    <option value="available">Tersedia</option>
                    <option value="rented">Disewa</option>
                    <option value="reserved">Dipesan</option>
                    <option value="maintenance">Maintenance</option>
                </select>
                <select name="view" class="rounded-xl border-slate-200 text-sm">
                    <option value="table">Tampilan Tabel</option>
                    <option value="grid">Tampilan Grid</option>
                </select>
                <button class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50">Filter</button>
            </div>
        </form>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama Aset</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Kondisi</th>
                    <th class="px-4 py-3 text-right">Tarif / Hari</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($assets as $asset)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold text-blue-600">{{ $asset->asset_code }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $asset->name }}</td>
                        <td class="px-4 py-3">{{ $asset->category?->name }}</td>
                        <td class="px-4 py-3">{{ $asset->location?->name }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$asset->availability_status" /></td>
                        <td class="px-4 py-3">{{ ucfirst($asset->condition_status) }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($asset->daily_rate, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('assets.show', $asset) }}" class="text-blue-600 hover:underline">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-slate-500">Belum ada data aset.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $assets->links() }}
    </div>
</x-app-layout>
