<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_code',
        'type',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'identity_type',
        'identity_number',
        'identity_file',
        'verification_status',
        'customer_level',
        'tag',
        'lifetime_value',
        'total_bookings',
        'customer_since',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'lifetime_value' => 'decimal:2',
        'customer_since' => 'date',
        'is_active' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getVerificationLabelAttribute(): string
    {
        return match ($this->verification_status) {
            'verified' => 'Terverifikasi',
            'pending' => 'Menunggu',
            'rejected' => 'Ditolak',
            default => 'Belum Dicek',
        };
    }
}
