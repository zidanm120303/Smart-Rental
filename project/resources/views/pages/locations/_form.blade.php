@php
    $locationItem ??= null;
    $method ??= 'POST';
    $action ??= route('locations.store');
    $submitLabel ??= $locationItem ? 'Simpan Lokasi' : 'Tambah Lokasi';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if (!in_array(strtoupper($method), ['POST'], true))
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <label>
            <span class="text-sm font-bold text-slate-700">Kode Lokasi</span>
            <input name="code" value="{{ old('code', $locationItem->code ?? '') }}" class="sr-input mt-2 w-full" placeholder="Contoh: GDG-010" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Nama Lokasi</span>
            <input name="name" value="{{ old('name', $locationItem->name ?? '') }}" class="sr-input mt-2 w-full" placeholder="Contoh: Gudang Utama Jakarta" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Tipe</span>
            <select name="type" class="sr-input mt-2 w-full" required>
                @foreach (['gudang' => 'Gudang', 'studio' => 'Studio', 'pickup' => 'Titik Pengambilan', 'rak' => 'Area Rak'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('type', $locationItem->type ?? 'gudang') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Kota</span>
            <input name="city" value="{{ old('city', $locationItem->city ?? '') }}" class="sr-input mt-2 w-full" placeholder="Jakarta">
        </label>
        <label class="md:col-span-2">
            <span class="text-sm font-bold text-slate-700">Telepon</span>
            <input name="phone" value="{{ old('phone', $locationItem->phone ?? '') }}" class="sr-input mt-2 w-full" placeholder="021-555-0123">
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-bold text-slate-700">Alamat</span>
        <textarea name="address" rows="3" class="sr-input mt-2 w-full" placeholder="Alamat lengkap lokasi">{{ old('address', $locationItem->address ?? '') }}</textarea>
    </label>

    <label class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3 text-sm font-semibold text-slate-700">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-600" @checked(old('is_active', $locationItem->is_active ?? true))>
        Lokasi aktif
    </label>

    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
        <button type="button" class="sr-button-secondary" onclick="this.closest('dialog')?.close()">Batal</button>
        <button class="sr-button-primary"><i data-lucide="save" class="h-4 w-4"></i> {{ $submitLabel }}</button>
    </div>
</form>
