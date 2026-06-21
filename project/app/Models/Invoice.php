<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_code',
        'booking_id',
        'customer_id',
        'issue_date',
        'due_date',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'deposit_paid',
        'total_amount',
        'paid_amount',
        'total_due',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'deposit_paid' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'total_due' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draf',
            'sent' => 'Terkirim',
            'paid' => 'Lunas',
            'partially_paid' => 'Dibayar Sebagian',
            'overdue' => 'Jatuh Tempo',
            default => 'Tidak Diketahui',
        };
    }
}
