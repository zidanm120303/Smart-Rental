<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetKit extends Model
{
    use SoftDeletes;

    protected $fillable = ['kit_code', 'name', 'description', 'daily_rate', 'is_active'];

    protected $casts = ['daily_rate' => 'decimal:2', 'is_active' => 'boolean'];

    public function items()
    {
        return $this->hasMany(AssetKitItem::class);
    }
}
