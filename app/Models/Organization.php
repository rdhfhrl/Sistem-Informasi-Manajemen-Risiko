<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organizations';
    protected $primaryKey = 'organization_id';

    protected $fillable = [
        'organization_name',
        'organization_type',
        'organization_code',
        'location',
        'organization_description',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Nilai default
    protected $attributes = [
        'organization_name' => 'UPTD PUPR Medan',
        'organization_type' => 'UPTD',
    ];

    // ========== RELATIONSHIPS ==========

    // Parent organization (Dinas PUPR)
    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_id', 'organization_id');
    }

    // Children organizations (jika ada)
    public function children()
    {
        return $this->hasMany(Organization::class, 'parent_id', 'organization_id');
    }

    // Risks yang terkait dengan organisasi ini
    public function risks()
    {
        return $this->hasMany(Risk::class, 'risk_organization_id', 'organization_id')->from('risk');
    }

    // Strategic Objectives
    public function strategicObjectives()
    {
        return $this->hasMany(StrategicObjective::class, 'strategic_objective_organization_id', 'organization_id');
    }

    // Business Processes
    public function businessProcesses()
    {
        return $this->hasMany(BusinessProcess::class, 'business_process_organization_id', 'organization_id');
    }

    // Audits
    public function audits()
    {
        return $this->hasMany(Audit::class, 'organization_id', 'organization_id');
    }

    // ========== SCOPES ==========

    // Scope untuk Dinas PUPR saja
    public function scopeDinas($query)
    {
        return $query->where('organization_type', 'Dinas');
    }

    // Scope untuk UPTD saja
    public function scopeUptd($query)
    {
        return $query->where('organization_type', 'UPTD');
    }

    // Scope untuk aktif saja
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope berdasarkan lokasi
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', '%' . $location . '%');
    }

    // ========== METHODS ==========

    // Cek apakah ini Dinas PUPR
    public function isDinas()
    {
        return $this->organization_type === 'Dinas';
    }

    // Cek apakah ini UPTD
    public function isUptd()
    {
        return $this->organization_type === 'UPTD';
    }

    // Cek apakah punya parent (hanya untuk UPTD)
    public function hasParent()
    {
        return !is_null($this->parent_id);
    }

    // Dapatkan Dinas PUPR parent
    public function getParentDinas()
    {
        if ($this->parent) {
            return $this->parent;
        }
        
        // Jika belum ada parent, cari Dinas PUPR
        return Organization::where('organization_type', 'Dinas')->first();
    }

    // Format nama lengkap dengan lokasi
    public function getFullName()
    {
        $name = $this->organization_name;
        if ($this->location) {
            $name .= ' - ' . $this->location;
        }
        if ($this->organization_code) {
            $name .= ' (' . $this->organization_code . ')';
        }
        return $name;
    }

    // Statistik organisasi
    public function getStats()
    {
        return [
            'risks_count' => $this->risks()->count(),
            'objectives_count' => $this->strategicObjectives()->count(),
            'processes_count' => $this->businessProcesses()->count(),
            'audits_count' => $this->audits()->count(),
        ];
    }

    // Validasi untuk parent_id
    public static function validateParentId($type, $parentId)
    {
        // Dinas tidak boleh punya parent
        if ($type === 'Dinas' && !is_null($parentId)) {
            return false;
        }
        
        // UPTD harus punya parent Dinas
        if ($type === 'UPTD') {
            if (is_null($parentId)) {
                return false;
            }
            
            // Parent harus Dinas
            $parent = Organization::find($parentId);
            if (!$parent || $parent->organization_type !== 'Dinas') {
                return false;
            }
        }
        
        return true;
    }

    // Buat Dinas PUPR default
    public static function createDefaultDinas()
    {
        return self::create([
            'organization_name' => 'Dinas Pekerjaan Umum dan Penataan Ruang Kota Medan',
            'organization_type' => 'Dinas',
            'organization_code' => 'DPUPR-MEDAN',
            'organization_description' => 'Dinas PUPR Kota Medan sebagai induk organisasi',
            'parent_id' => null,
            'is_active' => true,
        ]);
    }
}