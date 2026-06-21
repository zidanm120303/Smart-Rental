<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'icon', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
