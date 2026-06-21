@php
    $asset ??= null;
    $method ??= 'POST';
    $action ??= route('assets.store');
    $submitLabel ??= $asset ? 'Simpan Perubahan' : 'Tambah Aset';
    $imagePresets = [
        '/assets/equipment/flat/camera-sony-fx6.png' => 'Sony FX6 Cinema Camera',
        '/assets/equipment/flat/camera-canon-r5c.png' => 'Canon EOS R5 C',
        '/assets/equipment/flat/lens-canon-24-70.png' => 'Canon RF 24-70mm',
        '/assets/equipment/flat/drone-dji-mavic-3.png' => 'DJI Mavic 3 Pro',
        '/assets/equipment/flat/microphone-sennheiser.png' => 'Sennheiser AVX-ME2',
        '/assets/equipment/flat/speaker-jbl-eon712.png' => 'JBL EON712 Speaker',
        '/assets/equipment/flat/light-aputure-300d.png' => 'Aputure 300d II',
        '/assets/equipment/flat/tripod-manfrotto-504x.png' => 'Manfrotto 504X Tripod',
        '/assets/equipment/flat/mixer-yamaha-mg10xu.png' => 'Yamaha MG10XU Mixer',
        '/assets/equipment/flat/monitor-atomos-ninja-v.png' => 'Atomos Ninja V',
    ];
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if (!in_array(strtoupper($method), ['POST'], true))
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <label>
            <span class="text-sm font-bold text-slate-700">Kode Aset</span>
            <input name="asset_code" value="{{ old('asset_code', $asset->asset_code ?? '') }}" class="sr-input mt-2 w-full" placeholder="Contoh: AST-CAM-0100" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Nama Aset</span>
            <input name="name" value="{{ old('name', $asset->name ?? '') }}" class="sr-input mt-2 w-full" placeholder="Contoh: Sony FX6 Cinema Camera" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Kategori</span>
            <select name="category_id" class="sr-input mt-2 w-full" required>
                <option value="">Pilih kategori...</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $asset->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Merek</span>
            <select name="brand_id" class="sr-input mt-2 w-full">
                <option value="">Tanpa merek</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" @selected(old('brand_id', $asset->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Lokasi</span>
            <select name="location_id" class="sr-input mt-2 w-full" required>
                <option value="">Pilih lokasi...</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}" @selected(old('location_id', $asset->location_id ?? '') == $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Serial Number</span>
            <input name="serial_number" value="{{ old('serial_number', $asset->serial_number ?? '') }}" class="sr-input mt-2 w-full" placeholder="Nomor serial aset">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Tarif Harian</span>
            <input name="daily_rate" type="number" min="0" value="{{ old('daily_rate', $asset->daily_rate ?? 0) }}" class="sr-input mt-2 w-full" placeholder="1500000" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Deposit</span>
            <input name="deposit_amount" type="number" min="0" value="{{ old('deposit_amount', $asset->deposit_amount ?? 0) }}" class="sr-input mt-2 w-full" placeholder="2000000" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Harga Beli</span>
            <input name="purchase_price" type="number" min="0" value="{{ old('purchase_price', $asset->purchase_price ?? '') }}" class="sr-input mt-2 w-full" placeholder="65000000">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Nilai Pengganti</span>
            <input name="replacement_value" type="number" min="0" value="{{ old('replacement_value', $asset->replacement_value ?? '') }}" class="sr-input mt-2 w-full" placeholder="90000000">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Kondisi</span>
            <select name="condition_status" class="sr-input mt-2 w-full" required>
                @foreach (['excellent' => 'Sangat Baik', 'good' => 'Baik', 'fair' => 'Cukup', 'damaged' => 'Rusak'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('condition_status', $asset->condition_status ?? 'good') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Status Ketersediaan</span>
            <select name="availability_status" class="sr-input mt-2 w-full" required>
                @foreach (['available' => 'Tersedia', 'reserved' => 'Dipesan', 'rented' => 'Disewa', 'maintenance' => 'Perawatan', 'retired' => 'Diarsipkan'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('availability_status', $asset->availability_status ?? 'available') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Tanggal Beli</span>
            <input name="purchase_date" type="date" value="{{ old('purchase_date', optional($asset?->purchase_date ?? null)->format('Y-m-d')) }}" class="sr-input mt-2 w-full">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Perawatan Terakhir</span>
            <input name="last_maintenance_at" type="date" value="{{ old('last_maintenance_at', optional($asset?->last_maintenance_at ?? null)->format('Y-m-d')) }}" class="sr-input mt-2 w-full">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Posisi Rak</span>
            <input name="shelf_position" value="{{ old('shelf_position', $asset->shelf_position ?? '') }}" class="sr-input mt-2 w-full" placeholder="Rak Kamera A1">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Barcode</span>
            <input name="barcode" value="{{ old('barcode', $asset->barcode ?? '') }}" class="sr-input mt-2 w-full" placeholder="Kode barcode">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Utilisasi (%)</span>
            <input name="utilization_rate" type="number" min="0" max="100" value="{{ old('utilization_rate', $asset->utilization_rate ?? 0) }}" class="sr-input mt-2 w-full">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Total Disewa</span>
            <input name="total_rented" type="number" min="0" value="{{ old('total_rented', $asset->total_rented ?? 0) }}" class="sr-input mt-2 w-full">
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-bold text-slate-700">Gambar Produk Flat</span>
        <select name="image_url" class="sr-input mt-2 w-full">
            <option value="">Pilih gambar produk...</option>
            @foreach ($imagePresets as $path => $label)
                <option value="{{ $path }}" @selected(old('image_url', $asset->image_url ?? '') === $path)>{{ $label }}</option>
            @endforeach
            @if ($asset?->image_url && !array_key_exists($asset->image_url, $imagePresets))
                <option value="{{ $asset->image_url }}" selected>{{ $asset->image_url }}</option>
            @endif
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-bold text-slate-700">Deskripsi</span>
        <textarea name="description" rows="3" class="sr-input mt-2 w-full" placeholder="Deskripsi singkat aset">{{ old('description', $asset->description ?? '') }}</textarea>
    </label>

    <section class="rounded-2xl border border-slate-200 p-4">
        <h3 class="font-bold text-slate-950">Spesifikasi</h3>
        <div class="mt-3 grid gap-3 md:grid-cols-2">
            @foreach (range(0, 3) as $index)
                @php($spec = $asset?->specifications?->get($index))
                <input name="spec_name[]" value="{{ old('spec_name.' . $index, $spec->name ?? '') }}" class="sr-input" placeholder="Nama spesifikasi">
                <input name="spec_value[]" value="{{ old('spec_value.' . $index, $spec->value ?? '') }}" class="sr-input" placeholder="Nilai spesifikasi">
            @endforeach
        </div>
    </section>

    <label class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3 text-sm font-semibold text-slate-700">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-600" @checked(old('is_active', $asset->is_active ?? true))>
        Aset aktif dan dapat digunakan operasional
    </label>

    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
        <button type="button" class="sr-button-secondary" onclick="this.closest('dialog')?.close()">Batal</button>
        <button class="sr-button-primary"><i data-lucide="save" class="h-4 w-4"></i> {{ $submitLabel }}</button>
    </div>
</form>
