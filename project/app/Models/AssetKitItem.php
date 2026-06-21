<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetKitItem extends Model
{
    protected $fillable = ['asset_kit_id', 'asset_id', 'quantity'];

    public function kit()
    {
        return $this->belongsTo(AssetKit::class, 'asset_kit_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
