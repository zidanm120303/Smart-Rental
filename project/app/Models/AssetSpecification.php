<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetSpecification extends Model
{
    protected $fillable = ['asset_id', 'name', 'value', 'sort_order'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
