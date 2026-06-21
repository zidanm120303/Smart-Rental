@php
    $customer ??= null;
    $method ??= 'POST';
    $action ??= route('customers.store');
    $submitLabel ??= $customer ? 'Simpan Perubahan' : 'Tambah Pelanggan';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if (!in_array(strtoupper($method), ['POST'], true))
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <label>
            <span class="text-sm font-bold text-slate-700">Kode Pelanggan</span>
            <input name="customer_code" value="{{ old('customer_code', $customer->customer_code ?? '') }}" class="sr-input mt-2 w-full" placeholder="Contoh: CUST-0100" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Tipe Pelanggan</span>
            <select name="type" class="sr-input mt-2 w-full" required>
                <option value="company" @selected(old('type', $customer->type ?? 'company') === 'company')>Perusahaan</option>
                <option value="personal" @selected(old('type', $customer->type ?? '') === 'personal')>Perorangan</option>
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Nama Pelanggan</span>
            <input name="name" value="{{ old('name', $customer->name ?? '') }}" class="sr-input mt-2 w-full" placeholder="Contoh: Northline Studios" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Kontak Utama</span>
            <input name="contact_person" value="{{ old('contact_person', $customer->contact_person ?? '') }}" class="sr-input mt-2 w-full" placeholder="Nama PIC pelanggan">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Email</span>
            <input name="email" type="email" value="{{ old('email', $customer->email ?? '') }}" class="sr-input mt-2 w-full" placeholder="nama@perusahaan.co.id">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Telepon</span>
            <input name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="sr-input mt-2 w-full" placeholder="0812-0000-0000" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Kota</span>
            <input name="city" value="{{ old('city', $customer->city ?? '') }}" class="sr-input mt-2 w-full" placeholder="Jakarta">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Tag</span>
            <input name="tag" value="{{ old('tag', $customer->tag ?? '') }}" class="sr-input mt-2 w-full" placeholder="Production House">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Jenis Identitas</span>
            <input name="identity_type" value="{{ old('identity_type', $customer->identity_type ?? '') }}" class="sr-input mt-2 w-full" placeholder="KTP, NIB, atau NPWP">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Nomor Identitas</span>
            <input name="identity_number" value="{{ old('identity_number', $customer->identity_number ?? '') }}" class="sr-input mt-2 w-full" placeholder="Nomor identitas pelanggan">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Status Verifikasi</span>
            <select name="verification_status" class="sr-input mt-2 w-full" required>
                <option value="pending" @selected(old('verification_status', $customer->verification_status ?? 'pending') === 'pending')>Menunggu</option>
                <option value="verified" @selected(old('verification_status', $customer->verification_status ?? '') === 'verified')>Terverifikasi</option>
                <option value="rejected" @selected(old('verification_status', $customer->verification_status ?? '') === 'rejected')>Ditolak</option>
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Level Pelanggan</span>
            <select name="customer_level" class="sr-input mt-2 w-full" required>
                <option value="reguler" @selected(old('customer_level', $customer->customer_level ?? 'reguler') === 'reguler')>Reguler</option>
                <option value="vip" @selected(old('customer_level', $customer->customer_level ?? '') === 'vip')>VIP</option>
            </select>
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-bold text-slate-700">Alamat</span>
        <textarea name="address" rows="3" class="sr-input mt-2 w-full" placeholder="Alamat operasional pelanggan">{{ old('address', $customer->address ?? '') }}</textarea>
    </label>

    <label class="block">
        <span class="text-sm font-bold text-slate-700">Catatan</span>
        <textarea name="notes" rows="3" class="sr-input mt-2 w-full" placeholder="Catatan preferensi pickup, billing, atau kebutuhan khusus">{{ old('notes', $customer->notes ?? '') }}</textarea>
    </label>

    <label class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3 text-sm font-semibold text-slate-700">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-600" @checked(old('is_active', $customer->is_active ?? true))>
        Akun pelanggan aktif
    </label>

    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
        <button type="button" class="sr-button-secondary" onclick="this.closest('dialog')?.close()">Batal</button>
        <button class="sr-button-primary"><i data-lucide="save" class="h-4 w-4"></i> {{ $submitLabel }}</button>
    </div>
</form>
