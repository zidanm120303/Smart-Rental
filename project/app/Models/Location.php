<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'type', 'address', 'city', 'phone', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
