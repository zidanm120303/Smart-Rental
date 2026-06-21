<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        abort_unless(auth()->user()->hasPermission('payments.manage'), 403, 'Anda tidak memiliki akses mencatat pembayaran.');

        $validated = $request->validate([
            'payment_date' => ['required', 'date'],
            'method' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
            'method.required' => 'Metode pembayaran wajib dipilih.',
            'amount.required' => 'Nominal pembayaran wajib diisi.',
        ]);

        if ((float) $validated['amount'] > (float) $invoice->total_due) {
            return back()->withErrors(['amount' => 'Nominal pembayaran tidak boleh melebihi sisa tagihan.'])->withInput();
        }

        $invoice->payments()->create([
            'payment_code' => 'PAY-' . now()->format('YmdHis'),
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        $paidAmount = $invoice->payments()->sum('amount');
        $invoice->update([
            'paid_amount' => $paidAmount,
            'total_due' => max(0, (float) $invoice->total_amount - $paidAmount),
            'status' => $paidAmount >= (float) $invoice->total_amount ? 'paid' : 'partially_paid',
        ]);

        return back()->with('success', 'Pembayaran berhasil dicatat.');
    }
}
