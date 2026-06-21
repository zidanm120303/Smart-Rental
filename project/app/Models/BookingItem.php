<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    protected $fillable = [
        'booking_id',
        'asset_id',
        'daily_rate',
        'quantity',
        'rental_days',
        'line_total',
        'condition_out',
        'condition_in',
        'returned_at',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'rental_days' => 'decimal:2',
        'line_total' => 'decimal:2',
        'returned_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
