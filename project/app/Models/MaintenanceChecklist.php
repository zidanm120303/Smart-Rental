<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceChecklist extends Model
{
    protected $fillable = ['maintenance_request_id', 'label', 'is_checked', 'type'];

    protected $casts = ['is_checked' => 'boolean'];

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }
}
