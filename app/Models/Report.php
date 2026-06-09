<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reports';
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'report_type',
        'title',
        'period',
        'report_date',
        'organization_id',
        'project_id',
        'risk_id',
        'schedule_id',
        'data',
        'file_path',
        'status',
        'generated_by',
        'approved_by',
        'approval_date',
        'notes',
    ];

    protected $casts = [
        'data' => 'array',
        'report_date' => 'date',
        'approval_date' => 'date',
    ];

    protected $appends = [
        'report_type_label',
        'status_label',
        'status_color',
        'has_file',
        'file_size',
        'file_url',
        'is_published',
    ];

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class, 'risk_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ReportSchedule::class, 'schedule_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeGenerated($query)
    {
        return $query->where('status', 'generated');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeFromSchedule($query, $scheduleId)
    {
        return $query->where('schedule_id', $scheduleId);
    }

    public function scopeLastMonth($query)
    {
        return $query->where('report_date', '>=', Carbon::now()->subMonth());
    }

    public function scopeByOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByReportType($query, $reportType)
    {
        return $query->where('report_type', $reportType);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getReportTypeLabelAttribute(): string
    {
        return $this->getReportTypeOptions()[$this->report_type] ?? $this->report_type;
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'warning',
            'generated' => 'info',
            'published' => 'success',
            'archived' => 'secondary',
            default => 'light',
        };
    }

    public function getHasFileAttribute(): bool
    {
        return !empty($this->file_path) && Storage::exists($this->file_path);
    }

    public function getFileSizeAttribute(): ?string
    {
        if (!$this->has_file) {
            return null;
        }

        $size = Storage::size($this->file_path);
        
        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, 2) . ' KB';
        } else {
            return $size . ' bytes';
        }
    }

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->has_file) {
            return null;
        }

        return Storage::url($this->file_path);
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published';
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->status === 'draft';
    }

    public function getIsGeneratedAttribute(): bool
    {
        return $this->status === 'generated';
    }

    public function getIsArchivedAttribute(): bool
    {
        return $this->status === 'archived';
    }

    public function getFormattedReportDateAttribute(): string
    {
        return $this->report_date->format('d F Y');
    }

    public function getFormattedApprovalDateAttribute(): ?string
    {
        return $this->approval_date?->format('d F Y');
    }

    public function getGeneratedByNameAttribute(): string
    {
        return $this->generator->name ?? 'System';
    }

    public function getApprovedByNameAttribute(): ?string
    {
        return $this->approver->name ?? null;
    }

    // Methods
    public function getReportTypeOptions(): array
    {
        return [
            'monitoring' => 'Laporan Pemantauan',
            'risk_profile' => 'Profil Risiko',
            'executive_summary' => 'Ringkasan Eksekutif',
            'mitigation_effectiveness' => 'Efektivitas Mitigasi',
            'custom' => 'Laporan Kustom',
        ];
    }

    public function getStatusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'generated' => 'Generated',
            'published' => 'Published',
            'archived' => 'Archived',
        ];
    }

    public function getPeriodOptions(): array
    {
        return [
            'bulanan' => 'Bulanan',
            'triwulan' => 'Triwulan',
            'tahunan' => 'Tahunan',
            'custom' => 'Custom',
        ];
    }

    // Business Logic Methods
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isGenerated(): bool
    {
        return $this->status === 'generated';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function canEdit(): bool
    {
        return $this->isDraft() || $this->isGenerated();
    }

    public function canDelete(): bool
    {
        return !$this->isPublished();
    }

    public function canApprove(): bool
    {
        return $this->isGenerated() && auth()->check();
    }


    public function approve(User $approver, ?string $notes = null): bool
    {
        if (!$this->canApprove()) {
            return false;
        }

        $this->update([
            'status' => 'published',
            'approved_by' => $approver->id,
            'approval_date' => now(),
            'notes' => $notes ? $this->notes . "\n\nCatatan Persetujuan: " . $notes : $this->notes,
        ]);

        return true;
    }

    public function archive(): bool
    {
        $this->update(['status' => 'archived']);
        return true;
    }

    public function restoreFromArchive(): bool
    {
        if ($this->isArchived) {
            $this->update(['status' => 'published']);
            return true;
        }
        return false;
    }

    public function generateFileName(): string
    {
        $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '-', $this->title);
        $cleanTitle = preg_replace('/-+/', '-', $cleanTitle);
        
        return sprintf(
            'report-%s-%s-%s.pdf',
            $cleanTitle,
            $this->report_id,
            date('Ymd-His')
        );
    }

    public function getDataAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return $decoded ?? [];
        }
        
        return [];
    }

    public function setDataAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['data'] = json_encode($value);
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['data'] = $decoded ? json_encode($decoded) : json_encode([]);
        } else {
            $this->attributes['data'] = json_encode([]);
        }
    }

    public function getDataValue(string $key, $default = null)
    {
        return data_get($this->data, $key, $default);
    }

    public function setDataValue(string $key, $value): void
    {
        $data = $this->data;
        data_set($data, $key, $value);
        $this->data = $data;
        $this->save();
    }

    public function getSummaryData(): array
    {
        $data = $this->data;
        
        return [
            'title' => $this->title,
            'type' => $this->report_type_label,
            'period' => $this->period,
            'date' => $this->formatted_report_date,
            'status' => $this->status_label,
            'organization' => $this->organization->organization_name ?? 'Semua Organisasi',
            'project' => $this->project->pro_nama ?? 'Semua Proyek',
            'has_file' => $this->has_file,
            'file_size' => $this->file_size,
            'generated_by' => $this->generated_by_name,
            'approved_by' => $this->approved_by_name,
            'approval_date' => $this->formatted_approval_date,
            'schedule' => $this->schedule->schedule_name ?? 'Manual',
        ];
    }

    public function getStats(): array
    {
        $data = $this->data;
        
        // Extract common statistics from report data
        $stats = [
            'total_risks' => data_get($data, 'summary.total_risks', 0),
            'high_risk_count' => data_get($data, 'summary.high_risk_count', 0),
            'average_risk_score' => data_get($data, 'summary.average_risk_score', 0),
            'mitigation_completion_rate' => data_get($data, 'mitigation_effectiveness.completion_rate', 0),
        ];

        return array_filter($stats);
    }

    public function hasData(): bool
    {
        return !empty($this->data) && is_array($this->data);
    }

    public function hasSchedule(): bool
    {
        return !is_null($this->schedule_id);
    }

    public function getScheduleName(): string
    {
        return $this->schedule->schedule_name ?? 'Manual Generation';
    }

    // File Management Methods
    public function hasFile(): bool
    {
        return $this->has_file;
    }

    public function getFileSize(): ?string
    {
        return $this->file_size;
    }

    public function getFilePath(): ?string
    {
        return $this->file_path;
    }

    public function deleteFile(): bool
    {
        if ($this->has_file) {
            Storage::delete($this->file_path);
            $this->update(['file_path' => null]);
            return true;
        }
        return false;
    }

    // Query Helpers
    public static function getBySchedule(int $scheduleId)
    {
        return self::where('schedule_id', $scheduleId)
            ->orderBy('report_date', 'desc')
            ->get();
    }

    public static function getRecentReports($limit = 10)
    {
        return self::with(['organization', 'schedule'])
            ->orderBy('report_date', 'desc')
            ->take($limit)
            ->get();
    }

    public static function getReportCountByType(): array
    {
        return self::select('report_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('report_type')
            ->pluck('count', 'report_type')
            ->toArray();
    }

    public static function getReportCountByStatus(): array
    {
        return self::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public static function getMonthlyReportCount($months = 12): array
    {
        $results = self::selectRaw('DATE_FORMAT(report_date, "%Y-%m") as month, COUNT(*) as count')
            ->where('report_date', '>=', Carbon::now()->subMonths($months))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data = [];
        foreach ($results as $result) {
            $data[$result->month] = $result->count;
        }

        return $data;
    }
}