<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_code', 'category_id', 'brand_id', 'location_id', 'name', 'serial_number',
        'description', 'purchase_date', 'purchase_price', 'daily_rate', 'deposit_amount',
        'replacement_value', 'condition_status', 'availability_status', 'shelf_position',
        'qr_code', 'barcode', 'is_active', 'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'daily_rate' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'replacement_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category() { return $this->belongsTo(AssetCategory::class, 'category_id'); }
    public function brand() { return $this->belongsTo(AssetBrand::class, 'brand_id'); }
    public function location() { return $this->belongsTo(Location::class); }
    public function bookingItems() { return $this->hasMany(BookingItem::class); }
    public function maintenanceRequests() { return $this->hasMany(MaintenanceRequest::class); }

    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available')->where('is_active', true);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->availability_status) {
            'available' => 'Tersedia',
            'rented' => 'Disewa',
            'reserved' => 'Dipesan',
            'maintenance' => 'Dalam Maintenance',
            'retired' => 'Diarsipkan',
            default => 'Tidak Diketahui',
        };
    }
}
