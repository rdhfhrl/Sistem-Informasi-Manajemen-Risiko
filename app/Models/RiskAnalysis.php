<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskAnalysis extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'risk_analyses';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'risk_analysis_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'risk_analysis_risk_id',
        'likelihood_level',
        'impact_level',
        'risk_score',
        'risk_level',
        'analysis_date',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'analysis_date' => 'date',
        'likelihood_level' => 'integer',
        'impact_level' => 'integer',
        'risk_score' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the risk that owns the analysis.
     */
    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class, 'risk_analysis_risk_id', 'risk_id');
    }
    
    /**
     * Accessor for formatted analysis date.
     */
    public function getFormattedAnalysisDateAttribute(): string
    {
        return $this->analysis_date->format('d F Y');
    }
    
    /**
     * Accessor for risk level in Indonesian.
     */
    public function getRiskLevelIndonesianAttribute(): string
    {
        return match($this->risk_level) {
            'sangat_rendah' => 'Sangat Rendah',
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            'sangat_tinggi' => 'Sangat Tinggi',
            default => $this->risk_level,
        };
    }
    
    /**
     * Accessor for risk level color (for UI).
     */
    public function getRiskLevelColorAttribute(): string
    {
        return match($this->risk_level) {
            'sangat_rendah' => 'success',
            'rendah' => 'info',
            'sedang' => 'warning',
            'tinggi' => 'danger',
            'sangat_tinggi' => 'dark',
            default => 'secondary',
        };
    }
    
    /**
     * Scope a query to only include high risks.
     */
    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['tinggi', 'sangat_tinggi']);
    }
    
    /**
     * Scope a query to only include medium risks.
     */
    public function scopeMediumRisk($query)
    {
        return $query->where('risk_level', 'sedang');
    }
    
    /**
     * Scope a query to only include low risks.
     */
    public function scopeLowRisk($query)
    {
        return $query->whereIn('risk_level', ['rendah', 'sangat_rendah']);
    }
    
    /**
     * Scope a query to only include analyses for a specific risk.
     */
    public function scopeForRisk($query, $riskId)
    {
        return $query->where('risk_analysis_risk_id', $riskId);
    }
    
    /**
     * Scope a query to order by latest analysis date.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('analysis_date', 'desc');
    }
    
    /**
     * Calculate risk level based on score.
     */
    public static function calculateRiskLevel(int $score): string
    {
        if ($score >= 20) return 'sangat_tinggi';
        if ($score >= 15) return 'tinggi';
        if ($score >= 10) return 'sedang';
        if ($score >= 5) return 'rendah';
        return 'sangat_rendah';
    }
    
    /**
     * Calculate risk score from likelihood and impact.
     */
    public static function calculateRiskScore(int $likelihood, int $impact): int
    {
        return $likelihood * $impact;
    }
    
    /**
     * Get risk matrix position description.
     */
    public function getRiskMatrixPositionAttribute(): string
    {
        return "Likelihood: {$this->likelihood_level}/5, Impact: {$this->impact_level}/5";
    }
    
    /**
     * Check if this is the latest analysis for its risk.
     */
    public function isLatestAnalysis(): bool
    {
        $latest = self::where('risk_analysis_risk_id', $this->risk_analysis_risk_id)
            ->orderBy('analysis_date', 'desc')
            ->first();
            
        return $latest && $latest->risk_analysis_id === $this->risk_analysis_id;
    }
    
    /**
     * Get previous analysis for comparison.
     */
    public function previousAnalysis(): ?self
    {
        return self::where('risk_analysis_risk_id', $this->risk_analysis_risk_id)
            ->where('analysis_date', '<', $this->analysis_date)
            ->orderBy('analysis_date', 'desc')
            ->first();
    }
    
    /**
     * Get trend compared to previous analysis.
     */
    public function getTrendAttribute(): string
    {
        $previous = $this->previousAnalysis();
        
        if (!$previous) {
            return 'new';
        }
        
        $scoreChange = $this->risk_score - $previous->risk_score;
        
        if ($scoreChange > 0) return 'increase';
        if ($scoreChange < 0) return 'decrease';
        return 'stable';
    }
    
    /**
     * Get trend icon based on change.
     */
    public function getTrendIconAttribute(): string
    {
        return match($this->trend) {
            'increase' => 'arrow-up',
            'decrease' => 'arrow-down',
            'stable' => 'minus',
            default => 'circle',
        };
    }
    
    /**
     * Get trend color based on change.
     */
    public function getTrendColorAttribute(): string
    {
        return match($this->trend) {
            'increase' => 'danger',
            'decrease' => 'success',
            'stable' => 'warning',
            default => 'secondary',
        };
    }
}