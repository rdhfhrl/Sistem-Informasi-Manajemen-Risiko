<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskEvaluation extends Model
{
    use HasFactory;
    
    protected $table = 'risk_evaluations';

    protected $primaryKey = 'risk_evaluation_id';

    protected $fillable = [
        'risk_evaluation_risk_id',
        'risk_evaluation_priority',
        'mitigation_decision',
        'projected_risk_score',
        'evaluation_date',
    ];

    protected $casts = [
        'projected_risk_score' => 'decimal:2',
        'evaluation_date' => 'date',
    ];

    // Relationships
    public function risk()
    {
        return $this->belongsTo(Risk::class, 'risk_evaluation_risk_id', 'risk_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by', 'id');
    }

    public function getEvaluatorAttribute()
    {
        return null;
    }

    public function getPriorityColorAttribute()
    {
        return match ($this->risk_evaluation_priority) {
            'rendah' => 'success',
            'sedang' => 'info',
            'tinggi' => 'warning',
            'sangat tinggi' => 'danger',
            default => 'secondary',
        };
    }
}
