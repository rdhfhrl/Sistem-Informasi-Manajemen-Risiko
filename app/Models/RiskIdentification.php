<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskIdentification extends Model
{
    use HasFactory;

    protected $table = 'risk_identifications';
    protected $primaryKey = 'risk_identification_id';

    protected $fillable = [
        'risk_identification_risk_id',
        'loss_type',
        'violation_type',
        'failure_type',
        'error_type',
    ];

    protected $casts = [
        'loss_type' => 'string',
        'violation_type' => 'string',
        'failure_type' => 'string',
        'error_type' => 'string',
    ]; 

    // Relationships
    public function risk()
    {
        return $this->belongsTo(Risk::class, 'risk_identification_risk_id', 'risk_id');
    }
}
