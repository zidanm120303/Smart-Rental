@php
    $invoice ??= null;
    $method ??= 'POST';
    $action ??= route('invoices.store');
    $submitLabel ??= $invoice ? 'Simpan Tagihan' : 'Buat Tagihan';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if (!in_array(strtoupper($method), ['POST'], true))
        @method($method)
    @endif

    @unless ($invoice)
        <label class="block">
            <span class="text-sm font-bold text-slate-700">Referensi Pemesanan</span>
            <select name="booking_id" class="sr-input mt-2 w-full" required>
                <option value="">Pilih pemesanan yang belum memiliki tagihan...</option>
                @foreach ($bookings as $booking)
                    <option value="{{ $booking->id }}" @selected(old('booking_id') == $booking->id)>{{ $booking->booking_code }} - {{ $booking->customer->name }} - Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</option>
                @endforeach
            </select>
        </label>
    @endunless

    <div class="grid gap-4 md:grid-cols-2">
        <label>
            <span class="text-sm font-bold text-slate-700">Tanggal Terbit</span>
            <input name="issue_date" type="date" value="{{ old('issue_date', optional($invoice?->issue_date ?? now())->format('Y-m-d')) }}" class="sr-input mt-2 w-full" required>
        </label>
        <label>
            <span class="text-sm font-bold text-slate-700">Jatuh Tempo</span>
            <input name="due_date" type="date" value="{{ old('due_date', optional($invoice?->due_date ?? now()->addDays(14))->format('Y-m-d')) }}" class="sr-input mt-2 w-full" required>
        </label>
        <label class="md:col-span-2">
            <span class="text-sm font-bold text-slate-700">Status Tagihan</span>
            <select name="status" class="sr-input mt-2 w-full" required>
                @foreach (['draft' => 'Draf', 'sent' => 'Terkirim', 'partially_paid' => 'Dibayar Sebagian', 'paid' => 'Lunas', 'overdue' => 'Jatuh Tempo'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $invoice->status ?? 'sent') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-bold text-slate-700">Catatan Tagihan</span>
        <textarea name="notes" rows="4" class="sr-input mt-2 w-full" placeholder="Instruksi pembayaran atau catatan tambahan">{{ old('notes', $invoice->notes ?? 'Terima kasih sudah menggunakan layanan Smart Rental Pro.') }}</textarea>
    </label>

    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
        <button type="button" class="sr-button-secondary" onclick="this.closest('dialog')?.close()">Batal</button>
        <button class="sr-button-primary"><i data-lucide="save" class="h-4 w-4"></i> {{ $submitLabel }}</button>
    </div>
</form>
