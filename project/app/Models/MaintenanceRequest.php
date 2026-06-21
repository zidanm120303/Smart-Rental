<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'work_order_code',
        'asset_id',
        'reported_by',
        'assigned_to',
        'issue_title',
        'issue_type',
        'issue_description',
        'priority',
        'status',
        'progress',
        'scheduled_at',
        'completed_at',
        'estimated_cost',
        'actual_cost',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function checklists()
    {
        return $this->hasMany(MaintenanceChecklist::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new' => 'Baru',
            'in_progress' => 'Diproses',
            'waiting_parts' => 'Menunggu Suku Cadang',
            'completed' => 'Selesai',
            default => 'Tidak Diketahui',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
            default => 'Normal',
        };
    }
}
