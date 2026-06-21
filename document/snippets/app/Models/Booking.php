<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_code', 'customer_id', 'user_id', 'pickup_at', 'return_at', 'delivery_method',
        'status', 'subtotal', 'discount_amount', 'insurance_amount', 'delivery_fee',
        'tax_amount', 'deposit_amount', 'grand_total', 'notes', 'approved_by', 'approved_at',
        'cancelled_reason',
    ];

    protected $casts = [
        'pickup_at' => 'datetime',
        'return_at' => 'datetime',
        'approved_at' => 'datetime',
        'grand_total' => 'decimal:2',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(BookingItem::class); }
    public function invoice() { return $this->hasOne(Invoice::class); }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'overdue' => 'Terlambat',
            default => 'Tidak Diketahui',
        };
    }
}
