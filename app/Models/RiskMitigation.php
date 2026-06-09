<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskMitigation extends Model
{
    use HasFactory;

    protected $table = 'risk_mitigations';

    protected $primaryKey = 'risk_mitigation_id';

    protected $fillable = [
        'risk_mitigation_risk_id',
        'mitigation_plan',
        'responsible_party',
        'deadline',
        'status',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    // Relationships
    public function risk()
    {
        return $this->belongsTo(Risk::class, 'risk_id', 'risk_id');
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_party', 'id');
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'success',
            'in_progress' => 'primary',
            'delayed' => 'warning',
            'cancelled' => 'danger',
            'not_started' => 'secondary',
            default => 'secondary',
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->deadline < now() && $this->status !== 'completed';
    }

    // Scopes
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->where('status', '!=', 'completed');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'in_progress');
    }
}
