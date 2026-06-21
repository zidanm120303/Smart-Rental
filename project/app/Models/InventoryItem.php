<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['sku', 'name', 'category', 'location_id', 'stock', 'minimum_stock', 'unit', 'unit_cost', 'is_active'];

    protected $casts = ['unit_cost' => 'decimal:2', 'is_active' => 'boolean'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }
}
