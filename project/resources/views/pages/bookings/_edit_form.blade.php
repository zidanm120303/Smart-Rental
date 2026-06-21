@php
    $currentAssetIds = $booking->items->pluck('asset_id')->all();
@endphp

<form method="POST" action="{{ route('bookings.update', $booking) }}" class="space-y-5">
    @csrf
    @method('PUT')

    <div class="grid gap-4 md:grid-cols-2">
        <label>
            <span class="text-sm font-bold text-slate-700">Pelanggan</span>
            <select name="customer_id" class="sr-input mt-2 w-full" required>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(old('customer_id', $booking->customer_id) == $customer->id)>{{ $customer->name }} - {{ $customer->contact_person }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Status Pemesanan</span>
            <select name="status" class="sr-input mt-2 w-full" required>
                @foreach (['draft' => 'Draf', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'active' => 'Aktif', 'overdue' => 'Terlambat'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $booking->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Jadwal Pengambilan</span>
            <input name="pickup_at" value="{{ old('pickup_at', $booking->pickup_at->format('Y-m-d H:i')) }}" class="sr-input mt-2 w-full" placeholder="2026-06-20 09:00" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Jadwal Kembali</span>
            <input name="return_at" value="{{ old('return_at', $booking->return_at->format('Y-m-d H:i')) }}" class="sr-input mt-2 w-full" placeholder="2026-06-22 17:00" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Biaya Asuransi</span>
            <input name="insurance_amount" type="number" min="0" value="{{ old('insurance_amount', $booking->insurance_amount) }}" class="sr-input mt-2 w-full" placeholder="0">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Diskon</span>
            <input name="discount_amount" type="number" min="0" value="{{ old('discount_amount', $booking->discount_amount) }}" class="sr-input mt-2 w-full" placeholder="0">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Metode Pengiriman</span>
            <select name="delivery_method" class="sr-input mt-2 w-full" required>
                <option value="pickup" @selected(old('delivery_method', $booking->delivery_method) === 'pickup')>Ambil di Gudang</option>
                <option value="delivery" @selected(old('delivery_method', $booking->delivery_method) === 'delivery')>Dikirim ke Lokasi</option>
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Biaya Pengiriman</span>
            <input name="delivery_fee" type="number" min="0" value="{{ old('delivery_fee', $booking->delivery_fee) }}" class="sr-input mt-2 w-full" placeholder="0">
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-bold text-slate-700">Alamat Pengiriman</span>
        <input name="delivery_address" value="{{ old('delivery_address', $booking->delivery_address) }}" class="sr-input mt-2 w-full" placeholder="Alamat pengiriman jika memilih dikirim">
    </label>

    <section class="rounded-2xl border border-slate-200 p-4">
        <h3 class="font-bold text-slate-950">Aset Rental</h3>
        <p class="mt-1 text-sm text-slate-500">Pilih aset yang masuk dalam pemesanan ini.</p>
        <div class="mt-3 grid max-h-64 gap-2 overflow-y-auto pr-1 md:grid-cols-2">
            @foreach ($assets as $asset)
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}" class="rounded border-slate-300 text-blue-600" @checked(in_array($asset->id, old('asset_ids', $currentAssetIds)))>
                    <span class="min-w-0">
                        <span class="block truncate font-semibold text-slate-950">{{ $asset->name }}</span>
                        <span class="text-xs text-slate-500">{{ $asset->asset_code }} - Rp {{ number_format($asset->daily_rate, 0, ',', '.') }}/hari</span>
                    </span>
                </label>
            @endforeach
        </div>
    </section>

    <label class="block">
        <span class="text-sm font-bold text-slate-700">Catatan Operasional</span>
        <textarea name="notes" rows="3" class="sr-input mt-2 w-full" placeholder="Catatan persiapan aset">{{ old('notes', $booking->notes) }}</textarea>
    </label>

    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
        <button type="button" class="sr-button-secondary" onclick="this.closest('dialog')?.close()">Batal</button>
        <button class="sr-button-primary"><i data-lucide="save" class="h-4 w-4"></i> Simpan Perubahan</button>
    </div>
</form>
