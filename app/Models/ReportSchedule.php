<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;


class ReportSchedule extends Model
{
    use HasFactory;

    protected $table = 'report_schedules';
    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'schedule_name',
        'report_type',
        'frequency',
        'parameters',
        'recipients',
        'auto_generate',
        'auto_send_email',
        'generation_time',
        'day_of_month',
        'month_of_year',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'parameters' => 'array',
        'recipients' => 'array',
        'auto_generate' => 'boolean',
        'auto_send_email' => 'boolean',
        'is_active' => 'boolean',
        'generation_time' => 'datetime:H:i',
    ];

    protected $appends = [
        'frequency_label',
        'report_type_label',
        'status_label',
        'next_run_date',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'schedule_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAutoGenerate($query)
    {
        return $query->where('auto_generate', true);
    }

    public function scopeDueForGeneration($query)
    {
        return $query->where('is_active', true)
            ->where('auto_generate', true)
            ->where(function($q) {
                $q->where('generation_time', '<=', now()->format('H:i:s'))
                  ->orWhereNull('generation_time');
            });
    }

    // Accessors
    public function getFrequencyLabelAttribute(): string
    {
        return $this->getFrequencyOptions()[$this->frequency] ?? $this->frequency;
    }

    public function getReportTypeLabelAttribute(): string
    {
        return $this->getReportTypeOptions()[$this->report_type] ?? $this->report_type;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    public function getNextRunDateAttribute()
    {
        return $this->calculateNextRunDate();
    }

    // Methods
    public function getFrequencyOptions(): array
    {
        return [
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
            'quarterly' => 'Triwulan',
            'yearly' => 'Tahunan',
        ];
    }

    public function getReportTypeOptions(): array
    {
        return [
            'monitoring' => 'Laporan Pemantauan',
            'risk_profile' => 'Profil Risiko',
            'executive_summary' => 'Ringkasan Eksekutif',
            'mitigation_effectiveness' => 'Efektivitas Mitigasi',
        ];
    }

    public function calculateNextRunDate()
    {
        $now = now();
        
        switch ($this->frequency) {
            case 'daily':
                $nextRun = $now->copy();
                if ($this->generation_time && $now->format('H:i') >= $this->generation_time->format('H:i')) {
                    $nextRun->addDay();
                }
                break;
                
            case 'weekly':
                $nextRun = $now->copy()->next(Carbon::MONDAY);
                break;
                
            case 'monthly':
                $nextRun = $now->copy()->startOfMonth();
                if ($this->day_of_month) {
                    $nextRun->day($this->day_of_month);
                    if ($nextRun->lt($now) || ($nextRun->eq($now) && $this->generation_time && $now->format('H:i') >= $this->generation_time->format('H:i'))) {
                        $nextRun->addMonth();
                    }
                }
                break;
                
            case 'quarterly':
                $quarter = ceil($now->month / 3);
                $nextRun = Carbon::create($now->year, (($quarter - 1) * 3) + 1, 1);
                if ($nextRun->lt($now) || ($nextRun->eq($now) && $this->generation_time && $now->format('H:i') >= $this->generation_time->format('H:i'))) {
                    $nextRun->addMonths(3);
                }
                break;
                
            case 'yearly':
                $nextRun = Carbon::create($now->year, 1, 1);
                if ($nextRun->lt($now) || ($nextRun->eq($now) && $this->generation_time && $now->format('H:i') >= $this->generation_time->format('H:i'))) {
                    $nextRun->addYear();
                }
                if ($this->month_of_year) {
                    $monthNumber = array_search($this->month_of_year, [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ]) + 1;
                    $nextRun->month($monthNumber);
                }
                break;
                
            default:
                return null;
        }
        
        // Set time if specified
        if ($this->generation_time) {
            $nextRun->setTimeFrom($this->generation_time);
        } else {
            $nextRun->setTime(0, 0);
        }
        
        return $nextRun;
    }

    public function isDueForGeneration(): bool
    {
        if (!$this->is_active || !$this->auto_generate) {
            return false;
        }

        $nextRun = $this->calculateNextRunDate();
        return $nextRun && $nextRun->lte(now());
    }

    public function shouldGenerateNow(): bool
    {
        if (!$this->is_active || !$this->auto_generate) {
            return false;
        }

        // Check if it's the right time of day
        if ($this->generation_time) {
            $currentTime = now()->format('H:i');
            $scheduledTime = $this->generation_time->format('H:i');
            
            if ($currentTime < $scheduledTime) {
                return false;
            }
        }

        // Check based on frequency
        switch ($this->frequency) {
            case 'daily':
                $lastGenerated = $this->reports()
                    ->whereDate('created_at', today())
                    ->exists();
                return !$lastGenerated;
                
            case 'weekly':
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                $lastGenerated = $this->reports()
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->exists();
                return !$lastGenerated;
                
            case 'monthly':
                if ($this->day_of_month && now()->day != $this->day_of_month) {
                    return false;
                }
                $lastGenerated = $this->reports()
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->exists();
                return !$lastGenerated;
                
            case 'quarterly':
                $quarter = ceil(now()->month / 3);
                $startMonth = (($quarter - 1) * 3) + 1;
                $startDate = Carbon::create(now()->year, $startMonth, 1);
                $endDate = $startDate->copy()->addMonths(3)->subDay();
                
                $lastGenerated = $this->reports()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->exists();
                return !$lastGenerated;
                
            case 'yearly':
                if ($this->month_of_year) {
                    $monthNumber = array_search($this->month_of_year, [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ]) + 1;
                    if (now()->month != $monthNumber) {
                        return false;
                    }
                }
                $lastGenerated = $this->reports()
                    ->whereYear('created_at', now()->year)
                    ->exists();
                return !$lastGenerated;
        }

        return false;
    }

    public function getRecipientsArray(): array
    {
        return is_array($this->recipients) ? $this->recipients : [];
    }

    public function getParametersArray(): array
    {
        return is_array($this->parameters) ? $this->parameters : [];
    }

    public function canGenerate(): bool
    {
        return $this->is_active;
    }

    public function markAsGenerated()
    {
        // Method untuk mencatat bahwa schedule sudah digenerate
        // Bisa digunakan untuk logging atau tracking
        return true;
    }
}