<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('customers.view'), 403, 'Anda tidak memiliki akses ke Customer.');

        $customers = Customer::withCount(['bookings', 'invoices'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->tag, fn ($query, $tag) => $query->where('tag', $tag))
            ->when($request->status, fn ($query, $status) => $query->where('verification_status', $status))
            ->orderByDesc('lifetime_value')
            ->paginate(8)
            ->withQueryString();

        $selectedCustomer = Customer::with(['bookings.items.asset', 'invoices.payments'])
            ->find($request->get('customer_id')) ?? Customer::with(['bookings.items.asset', 'invoices.payments'])->orderByDesc('lifetime_value')->first();

        return view('pages.customers.index', [
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer,
            'tags' => Customer::whereNotNull('tag')->distinct()->pluck('tag'),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('customers.manage'), 403, 'Anda tidak memiliki akses menambah pelanggan.');

        $customer = Customer::create($this->validatedPayload($request));

        return redirect()->route('customers.index', ['customer_id' => $customer->id])->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer)
    {
        abort_unless(auth()->user()->hasPermission('customers.manage'), 403, 'Anda tidak memiliki akses mengubah pelanggan.');

        $customer->update($this->validatedPayload($request, $customer));

        return redirect()->route('customers.index', ['customer_id' => $customer->id])->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        abort_unless(auth()->user()->hasPermission('customers.manage'), 403, 'Anda tidak memiliki akses menghapus pelanggan.');

        if ($customer->bookings()->whereIn('status', ['pending', 'approved', 'active', 'overdue'])->exists()) {
            return back()->withErrors(['customer' => 'Pelanggan tidak dapat dihapus karena masih memiliki pemesanan aktif.']);
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    private function validatedPayload(Request $request, ?Customer $customer = null): array
    {
        $validated = $request->validate([
            'customer_code' => ['required', 'string', 'max:50', Rule::unique('customers', 'customer_code')->ignore($customer?->id)],
            'type' => ['required', 'in:personal,company'],
            'name' => ['required', 'string', 'max:180'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:80'],
            'identity_type' => ['nullable', 'string', 'max:50'],
            'identity_number' => ['nullable', 'string', 'max:100'],
            'verification_status' => ['required', 'in:pending,verified,rejected'],
            'customer_level' => ['required', 'in:reguler,vip'],
            'tag' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'customer_code.required' => 'Kode pelanggan wajib diisi.',
            'customer_code.unique' => 'Kode pelanggan sudah digunakan.',
            'name.required' => 'Nama pelanggan wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['customer_since'] = $customer?->customer_since ?? now()->toDateString();
        $validated['lifetime_value'] = $customer?->lifetime_value ?? 0;
        $validated['total_bookings'] = $customer?->total_bookings ?? 0;

        return $validated;
    }
}
