<form method="POST" action="{{ route('inventory.movements.store', $item) }}" class="space-y-5">
    @csrf
    <div class="rounded-2xl bg-slate-50 p-4">
        <p class="text-sm font-semibold text-slate-500">Item Inventori</p>
        <p class="mt-1 font-bold text-slate-950">{{ $item->sku }} - {{ $item->name }}</p>
        <p class="text-sm text-slate-500">Stok saat ini: {{ $item->stock }} {{ $item->unit }}</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <label>
            <span class="text-sm font-bold text-slate-700">Tipe Mutasi</span>
            <select name="type" class="sr-input mt-2 w-full" required>
                <option value="masuk">Stok Masuk</option>
                <option value="keluar">Stok Keluar</option>
                <option value="penyesuaian">Penyesuaian</option>
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Jumlah</span>
            <input name="quantity" type="number" min="1" class="sr-input mt-2 w-full" placeholder="Masukkan jumlah stok" required>
        </label>
        <label class="md:col-span-2">
            <span class="text-sm font-bold text-slate-700">Nomor Referensi</span>
            <input name="reference_number" class="sr-input mt-2 w-full" placeholder="Contoh: PO-2026-0100">
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-bold text-slate-700">Catatan Mutasi</span>
        <textarea name="notes" rows="3" class="sr-input mt-2 w-full" placeholder="Catatan pembelian, pemakaian, atau koreksi stok"></textarea>
    </label>

    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
        <button type="button" class="sr-button-secondary" onclick="this.closest('dialog')?.close()">Batal</button>
        <button class="sr-button-primary"><i data-lucide="arrow-left-right" class="h-4 w-4"></i> Simpan Mutasi</button>
    </div>
</form>
