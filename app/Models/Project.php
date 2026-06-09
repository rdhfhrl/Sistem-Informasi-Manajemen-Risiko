<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project';

    protected $primaryKey = 'pro_id';

    protected $keyType = 'int';
    
    public $incrementing = true;

    protected $fillable = [
        'pro_nama',
        'pro_lokasi',
        'pro_deskripsi',
        'pro_tanggal_mulai',
        'pro_tanggal_selesai',
        'pro_status',
    ];

    protected $casts = [
        'pro_tanggal_mulai' => 'date',
        'pro_tanggal_selesai' => 'date',
    ];

    // Relationships
    public function risks()
    {
        return $this->hasMany(Risk::class, 'risk_pro_id', 'pro_id');
    }

    public function risksCount()
    {
        return $this->hasMany(Risk::class, 'risk_pro_id', 'pro_id')->count();
    }

    public function highRisks()
    {
        return $this->hasMany(Risk::class, 'risk_pro_id', 'pro_id')
            ->whereIn('risk_level', ['tinggi', 'sangat_tinggi']);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('pro_status', 'Aktif');
    }

    public function scopeCompleted($query)
    {
        return $query->where('pro_status', 'Selesai');
    }

    public function scopeDelayed($query)
    {
        return $query->where('pro_status', 'Ditunda');
    }

    // Helper methods
    public function isActive()
    {
        return $this->pro_status === 'Aktif';
    }

    public function getProgressPercentage()
    {
        // Implement progress calculation logic
        return 0; // Placeholder
    }
}
