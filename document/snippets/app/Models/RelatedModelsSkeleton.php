<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'is_active'];
    public function assets() { return $this->hasMany(Asset::class, 'category_id'); }
}

class AssetBrand extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];
    public function assets() { return $this->hasMany(Asset::class, 'brand_id'); }
}

class Location extends Model
{
    protected $fillable = ['name', 'code', 'address', 'type', 'is_active'];
    public function assets() { return $this->hasMany(Asset::class); }
}

class Customer extends Model
{
    use SoftDeletes;
    protected $fillable = ['customer_code', 'type', 'name', 'company_name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'identity_number', 'tax_number', 'verification_status', 'notes', 'is_active'];
    public function bookings() { return $this->hasMany(Booking::class); }
}

class Invoice extends Model
{
    protected $fillable = ['invoice_code', 'booking_id', 'customer_id', 'issue_date', 'due_date', 'status', 'subtotal', 'discount_amount', 'tax_amount', 'deposit_paid', 'total_due', 'notes'];
}

class Payment extends Model
{
    protected $fillable = ['invoice_id', 'payment_code', 'paid_at', 'method', 'amount', 'reference_number', 'notes', 'received_by'];
}

class MaintenanceRequest extends Model
{
    protected $fillable = ['work_order_code', 'asset_id', 'reported_by', 'assigned_to', 'issue_type', 'priority', 'status', 'description', 'scheduled_at', 'completed_at', 'estimated_cost', 'actual_cost'];
}
