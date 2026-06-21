@php
    $item ??= null;
    $method ??= 'POST';
    $action ??= route('inventory.store');
    $submitLabel ??= $item ? 'Simpan Item' : 'Tambah Item';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if (!in_array(strtoupper($method), ['POST'], true))
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <label>
            <span class="text-sm font-bold text-slate-700">SKU</span>
            <input name="sku" value="{{ old('sku', $item->sku ?? '') }}" class="sr-input mt-2 w-full" placeholder="Contoh: INV-BAT-0100" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Nama Item</span>
            <input name="name" value="{{ old('name', $item->name ?? '') }}" class="sr-input mt-2 w-full" placeholder="Contoh: Baterai NP-FZ100" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Kategori</span>
            <input name="category" value="{{ old('category', $item->category ?? '') }}" class="sr-input mt-2 w-full" placeholder="Baterai, Kabel, Media" list="kategori-inventori" required>
            <datalist id="kategori-inventori">
                @foreach ($categories as $category)
                    <option value="{{ $category }}">
                @endforeach
            </datalist>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Lokasi</span>
            <select name="location_id" class="sr-input mt-2 w-full">
                <option value="">Tanpa lokasi</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}" @selected(old('location_id', $item->location_id ?? '') == $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Stok</span>
            <input name="stock" type="number" min="0" value="{{ old('stock', $item->stock ?? 0) }}" class="sr-input mt-2 w-full" placeholder="0" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Stok Minimum</span>
            <input name="minimum_stock" type="number" min="0" value="{{ old('minimum_stock', $item->minimum_stock ?? 0) }}" class="sr-input mt-2 w-full" placeholder="0" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Satuan</span>
            <input name="unit" value="{{ old('unit', $item->unit ?? 'pcs') }}" class="sr-input mt-2 w-full" placeholder="pcs, roll, paket" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Harga Satuan</span>
            <input name="unit_cost" type="number" min="0" value="{{ old('unit_cost', $item->unit_cost ?? 0) }}" class="sr-input mt-2 w-full" placeholder="0" required>
        </label>
    </div>

    <label class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3 text-sm font-semibold text-slate-700">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-600" @checked(old('is_active', $item->is_active ?? true))>
        Item aktif dan tampil di inventori
    </label>

    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
        <button type="button" class="sr-button-secondary" onclick="this.closest('dialog')?.close()">Batal</button>
        <button class="sr-button-primary"><i data-lucide="save" class="h-4 w-4"></i> {{ $submitLabel }}</button>
    </div>
</form>
