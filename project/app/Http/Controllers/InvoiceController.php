<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('invoices.view'), 403, 'Anda tidak memiliki akses ke Tagihan.');

        $invoices = Invoice::with(['customer', 'booking'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('invoice_code', 'like', "%{$search}%")
                        ->orWhereHas('booking', fn ($booking) => $booking->where('booking_code', 'like', "%{$search}%"))
                        ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $selectedInvoice = Invoice::with(['customer', 'booking.items.asset', 'items', 'payments'])
            ->find($request->get('invoice_id')) ?? Invoice::with(['customer', 'booking.items.asset', 'items', 'payments'])->latest()->first();

        return view('pages.invoices.index', [
            'invoices' => $invoices,
            'selectedInvoice' => $selectedInvoice,
            'bookings' => Booking::with('customer')
                ->whereDoesntHave('invoice')
                ->whereNotIn('status', ['draft', 'cancelled'])
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('invoices.manage'), 403, 'Anda tidak memiliki akses membuat tagihan.');

        $validated = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id', Rule::unique('invoices', 'booking_id')],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'status' => ['required', 'in:draft,sent,paid,partially_paid,overdue'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'booking_id.unique' => 'Pemesanan ini sudah memiliki tagihan.',
        ]);

        $booking = Booking::with('items.asset')->findOrFail($validated['booking_id']);
        $invoice = $this->createOrUpdateFromBooking($booking, $validated);

        return redirect()->route('invoices.index', ['invoice_id' => $invoice->id])->with('success', 'Tagihan berhasil dibuat.');
    }

    public function update(Request $request, Invoice $invoice)
    {
        abort_unless(auth()->user()->hasPermission('invoices.manage'), 403, 'Anda tidak memiliki akses mengubah tagihan.');

        $validated = $request->validate([
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'status' => ['required', 'in:draft,sent,paid,partially_paid,overdue'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $paidAmount = $invoice->payments()->sum('amount');
        if ($validated['status'] === 'paid') {
            $paidAmount = (float) $invoice->total_amount;
        }

        $invoice->update([
            ...$validated,
            'paid_amount' => $paidAmount,
            'total_due' => max(0, (float) $invoice->total_amount - $paidAmount),
        ]);

        return redirect()->route('invoices.index', ['invoice_id' => $invoice->id])->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice)
    {
        abort_unless(auth()->user()->hasPermission('invoices.manage'), 403, 'Anda tidak memiliki akses menghapus tagihan.');

        if ($invoice->payments()->exists()) {
            return back()->withErrors(['invoice' => 'Tagihan tidak dapat dihapus karena sudah memiliki pembayaran.']);
        }

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Tagihan berhasil dihapus.');
    }

    public function fromBooking(Booking $booking)
    {
        abort_unless(auth()->user()->hasPermission('invoices.manage'), 403, 'Anda tidak memiliki akses membuat tagihan.');

        $invoice = $this->createOrUpdateFromBooking($booking->load('items.asset'), [
            'issue_date' => today(),
            'due_date' => today()->addDays(14),
            'status' => 'draft',
            'notes' => 'Tagihan dibuat otomatis dari pemesanan.',
        ]);

        return redirect()->route('invoices.index', ['invoice_id' => $invoice->id])->with('success', 'Tagihan berhasil dibuat dari pemesanan.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        abort_unless(auth()->user()->hasPermission('invoices.view'), 403, 'Anda tidak memiliki akses mengunduh tagihan.');

        $invoice->load(['customer', 'booking.items.asset', 'items', 'payments']);

        return Pdf::loadView('pages.invoices.pdf', compact('invoice'))->download($invoice->invoice_code . '.pdf');
    }

    private function createOrUpdateFromBooking(Booking $booking, array $payload): Invoice
    {
        return DB::transaction(function () use ($booking, $payload) {
            $paidAmount = $payload['status'] === 'paid' ? (float) $booking->grand_total : 0;

            $invoice = Invoice::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'invoice_code' => 'INV-' . substr($booking->booking_code, 3),
                    'customer_id' => $booking->customer_id,
                    'issue_date' => $payload['issue_date'],
                    'due_date' => $payload['due_date'],
                    'status' => $payload['status'],
                    'subtotal' => $booking->subtotal,
                    'discount_amount' => $booking->discount_amount,
                    'tax_amount' => $booking->tax_amount,
                    'deposit_paid' => $booking->deposit_amount,
                    'total_amount' => $booking->grand_total,
                    'paid_amount' => $paidAmount,
                    'total_due' => max(0, (float) $booking->grand_total - $paidAmount),
                    'notes' => $payload['notes'] ?? 'Terima kasih atas kepercayaan Anda menggunakan Smart Rental Pro.',
                ]
            );

            $invoice->items()->delete();
            foreach ($booking->items as $item) {
                $invoice->items()->create([
                    'description' => $item->asset->name,
                    'rental_start' => $booking->pickup_at,
                    'rental_end' => $booking->return_at,
                    'quantity' => $item->quantity,
                    'rate' => $item->daily_rate,
                    'amount' => $item->line_total,
                ]);
            }

            return $invoice;
        });
    }
}
