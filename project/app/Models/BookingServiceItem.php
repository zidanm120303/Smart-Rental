<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingServiceItem extends Model
{
    protected $table = 'booking_services';

    protected $fillable = ['booking_id', 'name', 'amount'];

    protected $casts = ['amount' => 'decimal:2'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
