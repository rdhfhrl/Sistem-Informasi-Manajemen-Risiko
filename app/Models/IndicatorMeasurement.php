<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicatorMeasurement extends Model
{
    protected $table = 'indicator_measurements';
    protected $primaryKey = 'measurement_id';
    
    protected $fillable = [
        'risk_indicator_id',
        'measured_value',
        'measurement_date',
        'notes',
        'measured_by'
    ];
    
    protected $casts = [
        'measurement_date' => 'date',
    ];
    
    public function indicator()
    {
        return $this->belongsTo(RiskIndicator::class, 'risk_indicator_id');
    }
    
    public function measuredBy()
    {
        return $this->belongsTo(User::class, 'measured_by');
    }
}