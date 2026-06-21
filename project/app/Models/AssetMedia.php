<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMedia extends Model
{
    protected $fillable = ['asset_id', 'file_path', 'file_type', 'caption', 'sort_order'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
