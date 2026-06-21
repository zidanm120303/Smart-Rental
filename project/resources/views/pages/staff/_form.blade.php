@php
    $userItem ??= null;
    $method ??= 'POST';
    $action ??= route('staff.store');
    $submitLabel ??= $userItem ? 'Simpan Staf' : 'Tambah Staf';
    $selectedRole = old('role_id', $userItem?->roles?->first()?->id);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if (!in_array(strtoupper($method), ['POST'], true))
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <label>
            <span class="text-sm font-bold text-slate-700">Nama Staf</span>
            <input name="name" value="{{ old('name', $userItem->name ?? '') }}" class="sr-input mt-2 w-full" placeholder="Contoh: Admin Operasional" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Email</span>
            <input name="email" type="email" value="{{ old('email', $userItem->email ?? '') }}" class="sr-input mt-2 w-full" placeholder="nama@smartrental.local" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Telepon</span>
            <input name="phone" value="{{ old('phone', $userItem->phone ?? '') }}" class="sr-input mt-2 w-full" placeholder="0812-0000-0000">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Peran</span>
            <select name="role_id" class="sr-input mt-2 w-full" required>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected($selectedRole == $role->id)>{{ $role->display_name }}</option>
                @endforeach
            </select>
        </label>
        <label class="md:col-span-2">
            <span class="text-sm font-bold text-slate-700">{{ $userItem ? 'Password Baru' : 'Password' }}</span>
            <input name="password" type="password" class="sr-input mt-2 w-full" placeholder="{{ $userItem ? 'Kosongkan jika tidak diganti' : 'Minimal 8 karakter' }}" @required(!$userItem)>
        </label>
    </div>

    <label class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3 text-sm font-semibold text-slate-700">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-blue-600" @checked(old('is_active', $userItem->is_active ?? true))>
        Staf aktif dan dapat masuk sistem
    </label>

    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
        <button type="button" class="sr-button-secondary" onclick="this.closest('dialog')?.close()">Batal</button>
        <button class="sr-button-primary"><i data-lucide="save" class="h-4 w-4"></i> {{ $submitLabel }}</button>
    </div>
</form>
