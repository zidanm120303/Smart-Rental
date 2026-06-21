@php
    $requestItem ??= null;
    $method ??= 'POST';
    $action ??= route('maintenance.store');
    $submitLabel ??= $requestItem ? 'Simpan Perintah Kerja' : 'Buat Perintah Kerja';
    $checklistText = $requestItem?->checklists?->pluck('label')->join("\n") ?? "Inspeksi visual eksterior\nTes fungsi utama\nBersihkan dan kalibrasi\nCatat hasil servis";
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if (!in_array(strtoupper($method), ['POST'], true))
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <label>
            <span class="text-sm font-bold text-slate-700">Kode Work Order</span>
            <input name="work_order_code" value="{{ old('work_order_code', $requestItem->work_order_code ?? '') }}" class="sr-input mt-2 w-full" placeholder="Contoh: WO-2026-0100" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Aset</span>
            <select name="asset_id" class="sr-input mt-2 w-full" required>
                <option value="">Pilih aset...</option>
                @foreach ($assets as $asset)
                    <option value="{{ $asset->id }}" @selected(old('asset_id', $requestItem->asset_id ?? '') == $asset->id)>{{ $asset->asset_code }} - {{ $asset->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Judul Masalah</span>
            <input name="issue_title" value="{{ old('issue_title', $requestItem->issue_title ?? '') }}" class="sr-input mt-2 w-full" placeholder="Contoh: Pembersihan sensor kamera" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Jenis Masalah</span>
            <input name="issue_type" value="{{ old('issue_type', $requestItem->issue_type ?? '') }}" class="sr-input mt-2 w-full" placeholder="Sensor, audio, konektor, kalibrasi">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Prioritas</span>
            <select name="priority" class="sr-input mt-2 w-full" required>
                <option value="low" @selected(old('priority', $requestItem->priority ?? '') === 'low')>Rendah</option>
                <option value="medium" @selected(old('priority', $requestItem->priority ?? 'medium') === 'medium')>Sedang</option>
                <option value="high" @selected(old('priority', $requestItem->priority ?? '') === 'high')>Tinggi</option>
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Status</span>
            <select name="status" class="sr-input mt-2 w-full" required>
                <option value="new" @selected(old('status', $requestItem->status ?? 'new') === 'new')>Baru</option>
                <option value="in_progress" @selected(old('status', $requestItem->status ?? '') === 'in_progress')>Diproses</option>
                <option value="waiting_parts" @selected(old('status', $requestItem->status ?? '') === 'waiting_parts')>Menunggu Suku Cadang</option>
                <option value="completed" @selected(old('status', $requestItem->status ?? '') === 'completed')>Selesai</option>
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Teknisi</span>
            <select name="assigned_to" class="sr-input mt-2 w-full">
                <option value="">Belum ditugaskan</option>
                @foreach ($technicians as $technician)
                    <option value="{{ $technician->id }}" @selected(old('assigned_to', $requestItem->assigned_to ?? '') == $technician->id)>{{ $technician->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Jadwal Perawatan</span>
            <input name="scheduled_at" type="datetime-local" value="{{ old('scheduled_at', optional($requestItem?->scheduled_at)->format('Y-m-d\TH:i')) }}" class="sr-input mt-2 w-full">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Progres (%)</span>
            <input name="progress" type="number" min="0" max="100" value="{{ old('progress', $requestItem->progress ?? 0) }}" class="sr-input mt-2 w-full">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Estimasi Biaya</span>
            <input name="estimated_cost" type="number" min="0" value="{{ old('estimated_cost', $requestItem->estimated_cost ?? 0) }}" class="sr-input mt-2 w-full" placeholder="0">
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Biaya Aktual</span>
            <input name="actual_cost" type="number" min="0" value="{{ old('actual_cost', $requestItem->actual_cost ?? 0) }}" class="sr-input mt-2 w-full" placeholder="0">
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-bold text-slate-700">Deskripsi Masalah</span>
        <textarea name="issue_description" rows="4" class="sr-input mt-2 w-full" placeholder="Jelaskan gejala, kondisi aset, dan tindakan yang dibutuhkan" required>{{ old('issue_description', $requestItem->issue_description ?? '') }}</textarea>
    </label>

    <label class="block">
        <span class="text-sm font-bold text-slate-700">Daftar Cek</span>
        <textarea name="checklist_labels" rows="4" class="sr-input mt-2 w-full" placeholder="Satu daftar cek per baris">{{ old('checklist_labels', $checklistText) }}</textarea>
    </label>

    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
        <button type="button" class="sr-button-secondary" onclick="this.closest('dialog')?.close()">Batal</button>
        <button class="sr-button-primary"><i data-lucide="save" class="h-4 w-4"></i> {{ $submitLabel }}</button>
    </div>
</form>
