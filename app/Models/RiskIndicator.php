<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskIndicator extends Model
{
    use HasFactory;

    protected $table = 'risk_indicators';
    protected $primaryKey = 'risk_indicator_id';

    protected $fillable = [
        'risk_indicator_risk_id',
        'indicator_type',
        'indicator_name',
        'indicator_description',
        'threshold',
        'unit',
    ];

    protected $casts = [
        'threshold' => 'decimal:2',
    ];

    // Relationships
    public function risk()
    {
        return $this->belongsTo(Risk::class, 'risk_indicator_risk_id', 'risk_id');
    }

    public function measurements()
    {
        return $this->hasMany(IndicatorMeasurement::class, 'risk_indicator_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotNull('risk_indicator_risk_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('indicator_type', $type);
    }

    public function scopeByRiskLevel($query, $level)
    {
        return $query->whereHas('risk', function($q) use ($level) {
            $q->where('risk_level', $level);
        });
    }

    // Helper methods - Untuk mendapatkan nilai terkini dari measurements
    public function getCurrentValueAttribute()
    {
        $latestMeasurement = $this->measurements()->latest()->first();
        return $latestMeasurement ? $latestMeasurement->measured_value : null;
    }

    public function getLastMeasurementDateAttribute()
    {
        $latestMeasurement = $this->measurements()->latest()->first();
        return $latestMeasurement ? $latestMeasurement->measurement_date : null;
    }

    public function getDataSourceAttribute()
    {
        return null; // Tidak ada di database
    }

    public function getMeasurementFrequencyAttribute()
    {
        return null; // Tidak ada di database
    }

    public function isExceeded()
    {
        $currentValue = $this->current_value;
        if (is_null($currentValue)) {
            return false;
        }
        return $currentValue > $this->threshold;
    }

    public function getIndicatorTypeLabelAttribute()
    {
        return match($this->indicator_type) {
            'akar_masalah' => 'Akar Masalah',
            'penyebab' => 'Penyebab',
            'dampak' => 'Dampak',
            'lainnya' => 'Lainnya',
            default => 'Tidak Diketahui',
        };
    }

    public function getExceededStatusAttribute()
    {
        $currentValue = $this->current_value;
        
        if (is_null($currentValue)) {
            return ['status' => 'not_measured', 'label' => 'Belum Diukur', 'color' => 'gray'];
        }

        if ($currentValue > $this->threshold) {
            return ['status' => 'exceeded', 'label' => 'Melebihi Batas', 'color' => 'red'];
        }

        return ['status' => 'normal', 'label' => 'Normal', 'color' => 'green'];
    }

    public function updateMeasurement($value, $date, $notes = null, $measuredBy = null)
    {
        return IndicatorMeasurement::create([
            'risk_indicator_id' => $this->risk_indicator_id,
            'measured_value' => $value,
            'measurement_date' => $date,
            'notes' => $notes,
            'measured_by' => $measuredBy ?? auth()->id(),
        ]);
    }

    public function getMeasurementCountAttribute()
    {
        return $this->measurements()->count();
    }

    public function getMeasurementHistory($limit = 10)
    {
        return $this->measurements()
            ->with('measuredBy')
            ->orderBy('measurement_date', 'desc')
            ->limit($limit)
            ->get();
    }
}