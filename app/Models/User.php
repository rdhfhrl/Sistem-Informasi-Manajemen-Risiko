<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',          
        'address',        
        'avatar',         
        'preferences',
        'organization_id',
        'is_active', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean', 
        ];
    }

    /**
     * Scope untuk user aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk user tidak aktif
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Aktifkan user
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
        return $this;
    }

    /**
     * Nonaktifkan user
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
        return $this;
    }

    // Relationship
    public function risks()
    {
        return $this->hasMany(Risk::class, 'risk_user_id', 'id');
    }

    public function mitigations()
    {
        return $this->hasMany(RiskMitigation::class, 'responsible_party', 'id');
    }

    public function evaluations()
    {
        return $this->hasMany(RiskEvaluation::class, 'evaluated_by', 'id');
    }

    public function monitorings()
    {
        return $this->hasMany(RiskMonitoring::class, 'monitored_by', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }

    // Scope for roles
    public function scopeRiskOwners($query)
    {
        return $query->where('role', 'unit_pemilik_risiko');
    }

    public function scopeAuditors($query)
    {
        return $query->where('role', 'auditor');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // check user role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUnitPemilikRisiko()
    {
        return $this->role === 'unit_pemilik_risiko';
    }

    public function isAuditor()
    {
        return $this->role === 'auditor';
    }

    public function isActive()
    {
        return $this->is_active === true;
    }

    public function getRoleNameAttribute()
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'unit_pemilik_risiko' => 'Unit Pemilik Risiko',
            'auditor' => 'Auditor',
            default => 'User',
        };
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->is_active) {
            return '<span class="badge bg-success">Aktif</span>';
        } else {
            return '<span class="badge bg-danger">Tidak Aktif</span>';
        }
    }
}