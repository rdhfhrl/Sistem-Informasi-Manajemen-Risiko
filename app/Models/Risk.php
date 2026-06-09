<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    use HasFactory;
    
    protected $table = 'risk';
    protected $primaryKey = 'risk_id';
    
    protected $fillable = [
        'risk_code',
        'risk_pro_id',
        'risk_organization_id',
        'risk_strategic_objective_id',
        'risk_business_process_id',
        'risk_category_id',
        'risk_description',
        'risk_user_id',
        'risk_level',
        'risk_score',
        'likelihood_level',
        'impact_level',
        'last_analysis_date',
        'last_monitoring_date',
        'last_evaluation_date',
        'risk_status',
        'identified_at',
        'identified_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'risk_score' => 'decimal:2',
        'identified_at' => 'datetime',
        'last_analysis_date' => 'date',
        'last_monitoring_date' => 'date',
        'last_evaluation_date' => 'date'
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class, 'risk_pro_id', 'pro_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'risk_organization_id', 'organization_id');
    }

    public function strategicObjective()
    {
        return $this->belongsTo(StrategicObjective::class, 'risk_strategic_objective_id', 'strategic_objective_id');
    }

    public function businessProcess()
    {
        return $this->belongsTo(BusinessProcess::class, 'risk_business_process_id', 'business_process_id');
    }

    public function category()
    {
        return $this->belongsTo(RiskCategory::class, 'risk_category_id', 'risk_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'risk_user_id', 'id');
    }

    public function identifier()
    {
        return $this->belongsTo(User::class, 'identified_by', 'id');
    }

    public function identification()
    {
        return $this->hasOne(RiskIdentification::class, 'risk_identification_risk_id', 'risk_id');
    }

    public function analyses()
    {
        return $this->hasMany(RiskAnalysis::class, 'risk_analysis_risk_id', 'risk_id');
    }

    public function evaluations()
    {
        return $this->hasMany(RiskEvaluation::class, 'risk_evaluation_risk_id', 'risk_id');
    }

    public function indicators()
    {
        return $this->hasMany(RiskIndicator::class, 'risk_indicator_risk_id', 'risk_id');
    }

    public function mitigations()
    {
        return $this->hasMany(RiskMitigation::class, 'risk_mitigation_risk_id', 'risk_id');
    }

    public function monitorings()
    {
        return $this->hasMany(RiskMonitoring::class, 'risk_monitoring_risk_id', 'risk_id');
    }

    public function audits()
    {
        return $this->hasMany(Audit::class, 'risk_id', 'risk_id');
    }

    // Scopes
    public function scopeByOrganization($query, $organizationId)
    {
        return $query->where('risk_organization_id', $organizationId);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('risk_pro_id', $projectId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('risk_category_id', $categoryId);
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['tinggi', 'sangat_tinggi']);
    }

    public function scopeMediumRisk($query)
    {
        return $query->where('risk_level', 'sedang');
    }

    public function scopeLowRisk($query)
    {
        return $query->whereIn('risk_level', ['rendah', 'sangat_rendah']);
    }

    public function scopeActive($query)
    {
        return $query->where('risk_status', 'active');
    }

    public function scopeMonitoring($query)
    {
        return $query->where('risk_status', 'monitoring');
    }

    public function scopeDueForMonitoring($query, $days = 30)
    {
        $dateThreshold = now()->subDays($days);
        return $query->where(function($q) use ($dateThreshold) {
            $q->whereNull('last_monitoring_date')
              ->orWhere('last_monitoring_date', '<', $dateThreshold);
        })->whereIn('risk_level', ['tinggi', 'sangat_tinggi']);
    }

    public function scopeOverdueForMonitoring($query, $days = 30)
    {
        $dateThreshold = now()->subDays($days);
        return $query->where('last_monitoring_date', '<', $dateThreshold)
                    ->whereIn('risk_level', ['tinggi', 'sangat_tinggi']);
    }

    // Helper methods
    public function getLatestAnalysis()
    {
        return $this->analyses()->latest()->first();
    }

    public function getLatestEvaluation()
    {
        return $this->evaluations()->latest()->first();
    }

    public function getLatestMonitoring()
    {
        return $this->monitorings()->latest('monitoring_date')->first();
    }

    public function getLatestMitigation()
    {
        return $this->mitigations()->latest()->first();
    }

    public function getAnalysisCountAttribute()
    {
        return $this->analyses()->count();
    }

    public function getEvaluationCountAttribute()
    {
        return $this->evaluations()->count();
    }

    public function getMonitoringCountAttribute()
    {
        return $this->monitorings()->count();
    }

    public function getMitigationCountAttribute()
    {
        return $this->mitigations()->count();
    }

    // Attribute Accessors
    public function getRiskLevelLabelAttribute()
    {
        return match($this->risk_level) {
            'sangat_rendah' => 'Sangat Rendah',
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            'sangat_tinggi' => 'Sangat Tinggi',
            default => 'Belum Dianalisis',
        };
    }

    public function getLikelihoodLevelLabelAttribute()
    {
        return match($this->likelihood_level) {
            'sangat_rendah' => 'Sangat Rendah',
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            'sangat_tinggi' => 'Sangat Tinggi',
            default => 'Tidak Diketahui',
        };
    }

    public function getImpactLevelLabelAttribute()
    {
        return match($this->impact_level) {
            'sangat_rendah' => 'Sangat Rendah',
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            'sangat_tinggi' => 'Sangat Tinggi',
            default => 'Tidak Diketahui',
        };
    }

    public function getRiskStatusLabelAttribute()
    {
        return match($this->risk_status) {
            'draft' => 'Draft',
            'active' => 'Aktif',
            'monitoring' => 'Dalam Pemantauan',
            'mitigated' => 'Telah Dimitigasi',
            'closed' => 'Ditutup',
            default => 'Tidak Diketahui',
        };
    }

    public function getRiskStatusColorAttribute()
    {
        return match($this->risk_status) {
            'draft' => 'gray',
            'active' => 'blue',
            'monitoring' => 'yellow',
            'mitigated' => 'green',
            'closed' => 'red',
            default => 'gray',
        };
    }

    public function getRiskLevelColorAttribute()
    {
        return match($this->risk_level) {
            'sangat_rendah' => 'green',
            'rendah' => 'blue',
            'sedang' => 'yellow',
            'tinggi' => 'orange',
            'sangat_tinggi' => 'red',
            default => 'gray',
        };
    }

    public function getDaysSinceLastMonitoringAttribute()
    {
        if (!$this->last_monitoring_date) {
            return null;
        }
        
        return now()->diffInDays($this->last_monitoring_date);
    }

    public function getIsDueForMonitoringAttribute()
    {
        if (!$this->last_monitoring_date) {
            return true; // Belum pernah dimonitor
        }
        
        $daysSince = $this->days_since_last_monitoring;
        $isHighRisk = in_array($this->risk_level, ['tinggi', 'sangat_tinggi']);
        
        return $isHighRisk && $daysSince >= 30;
    }

    public function getRouteKeyName()
    {
        return 'risk_id';
    }

    // Business Logic Methods
    public function updateFromLatestMonitoring()
    {
        $latestMonitoring = $this->getLatestMonitoring();
        
        if ($latestMonitoring) {
            $this->update([
                'risk_score' => $latestMonitoring->current_risk_score,
                'risk_level' => $latestMonitoring->current_risk_level,
                'last_monitoring_date' => $latestMonitoring->monitoring_date,
                'risk_status' => 'monitoring'
            ]);
        }
    }

    public function updateFromLatestAnalysis()
    {
        $latestAnalysis = $this->getLatestAnalysis();
        
        if ($latestAnalysis) {
            $this->update([
                'risk_score' => $latestAnalysis->risk_score,
                'risk_level' => $latestAnalysis->risk_level,
                'likelihood_level' => $latestAnalysis->likelihood_level,
                'impact_level' => $latestAnalysis->impact_level,
                'last_analysis_date' => $latestAnalysis->analysis_date,
                'risk_status' => 'active'
            ]);
        }
    }

    public function updateFromLatestEvaluation()
    {
        $latestEvaluation = $this->getLatestEvaluation();
        
        if ($latestEvaluation) {
            $this->update([
                'last_evaluation_date' => $latestEvaluation->evaluation_date,
                'risk_status' => $latestEvaluation->evaluation_status
            ]);
        }
    }

    // Static Methods
    public static function calculateRiskLevelFromScore($score)
    {
        if ($score >= 20) return 'sangat_tinggi';
        if ($score >= 15) return 'tinggi';
        if ($score >= 10) return 'sedang';
        if ($score >= 5) return 'rendah';
        return 'sangat_rendah';
    }
}