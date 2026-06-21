<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('bookings.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'asset_ids' => ['required', 'array', 'min:1'],
            'asset_ids.*' => ['required', 'integer', 'exists:assets,id'],
            'pickup_at' => ['required', 'date', 'after_or_equal:now'],
            'return_at' => ['required', 'date', 'after:pickup_at'],
            'delivery_method' => ['required', 'in:pickup,delivery'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'insurance_amount' => ['nullable', 'numeric', 'min:0'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'in:draft,pending'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer wajib dipilih.',
            'asset_ids.required' => 'Minimal satu aset wajib dipilih.',
            'pickup_at.required' => 'Tanggal dan jam pickup wajib diisi.',
            'return_at.after' => 'Tanggal kembali harus setelah tanggal pickup.',
        ];
    }
}
