<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['payment_code', 'invoice_id', 'user_id', 'payment_date', 'method', 'amount', 'reference_number', 'notes'];

    protected $casts = ['payment_date' => 'date', 'amount' => 'decimal:2'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
