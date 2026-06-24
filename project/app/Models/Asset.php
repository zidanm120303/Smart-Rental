<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_code',
        'category_id',
        'brand_id',
        'location_id',
        'name',
        'serial_number',
        'description',
        'purchase_date',
        'purchase_price',
        'daily_rate',
        'deposit_amount',
        'replacement_value',
        'condition_status',
        'availability_status',
        'shelf_position',
        'image_url',
        'qr_code',
        'barcode',
        'utilization_rate',
        'total_rented',
        'last_maintenance_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'last_maintenance_at' => 'date',
        'purchase_price' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'replacement_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(AssetBrand::class, 'brand_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function media()
    {
        return $this->hasMany(AssetMedia::class);
    }

    public function primaryMedia()
    {
        return $this->hasOne(AssetMedia::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getDisplayImageUrlAttribute(): ?string
    {
        if ($this->relationLoaded('primaryMedia')) {
            return $this->primaryMedia?->file_path ?? $this->image_url;
        }

        return $this->primaryMedia()->value('file_path') ?? $this->image_url;
    }

    public function specifications()
    {
        return $this->hasMany(AssetSpecification::class)->orderBy('sort_order');
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

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
            'maintenance' => 'Perawatan',
            'retired' => 'Diarsipkan',
            default => 'Tidak Diketahui',
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition_status) {
            'excellent' => 'Sangat Baik',
            'good' => 'Baik',
            'fair' => 'Cukup',
            'damaged' => 'Rusak',
            default => 'Tidak Diketahui',
        };
    }
}
