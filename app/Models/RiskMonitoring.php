<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskMonitoring extends Model
{
    use HasFactory;
     
    protected $table = 'risk_monitorings';
    protected $primaryKey = 'risk_monitoring_id';

    protected $fillable = [
        'risk_monitoring_risk_id',
        'monitoring_date',
        'current_risk_score',
        'monitoring_result',
        'monitoring_report',
        'current_risk_level',
        'effectiveness_rating',
        'monitored_by',
        'next_monitoring_date',
        'recommendations',
    ];

    protected $casts = [
        'monitoring_date' => 'date',
        'current_risk_score' => 'decimal:2',
        'next_monitoring_date' => 'date',
    ];

     protected static function booted()
    {
        static::saved(function ($monitoring) {
            // Update risk data
            $risk = $monitoring->risk;
            if ($risk) {
                $risk->updateFromLatestMonitoring();
            }
        });

        static::deleted(function ($monitoring) {
            // Update risk dengan monitoring terbaru yang tersisa
            $risk = $monitoring->risk;
            if ($risk) {
                $risk->updateFromLatestMonitoring();
            }
        });
    }

    // Relationships
    public function risk()
    {
        return $this->belongsTo(Risk::class, 'risk_monitoring_risk_id', 'risk_id');
    }

    // Method untuk menghitung level risiko berdasarkan skor
    public function calculateRiskLevel($score = null)
    {
        $score = $score ?? $this->current_risk_score;
        
        if ($score >= 20) return 'sangat_tinggi';
        if ($score >= 15) return 'tinggi';
        if ($score >= 10) return 'sedang';
        if ($score >= 5) return 'rendah';
        return 'sangat_rendah';
    }

    // Method untuk mendapatkan label level risiko
    public function getRiskLevelLabelAttribute()
    {
        return match($this->current_risk_level) {
            'sangat_rendah' => 'Sangat Rendah',
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            'sangat_tinggi' => 'Sangat Tinggi',
            default => 'Tidak Diketahui',
        };
    }

    // Method untuk mendapatkan label efektivitas
    public function getEffectivenessLabelAttribute()
    {
        if (!$this->effectiveness_rating) return null;
        
        return match($this->effectiveness_rating) {
            1 => 'Sangat Tidak Efektif',
            2 => 'Tidak Efektif',
            3 => 'Cukup Efektif',
            4 => 'Efektif',
            5 => 'Sangat Efektif',
            default => 'Tidak Diketahui',
        };
    }

    // Scope untuk pemantauan yang sudah lewat jadwal
    public function scopeOverdue($query)
    {
        return $query->whereNotNull('next_monitoring_date')
                    ->where('next_monitoring_date', '<', now());
    }

    // Scope untuk pemantauan dengan level risiko tinggi
    public function scopeHighRisk($query)
    {
        return $query->whereIn('current_risk_level', ['tinggi', 'sangat_tinggi']);
    }
}