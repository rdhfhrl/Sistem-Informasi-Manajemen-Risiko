<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    protected $table = 'audits';
    protected $primaryKey = 'audit_id';

    protected $fillable = [
        'risk_id',
        'auditor',
        'audit_date',
        'audit_findings',
        'audit_recommendations',
        'audit_report',
    ];

    protected $casts = [
        'audit_date' => 'date',
    ];

    // Relationships
    public function risk()
    {
        return $this->belongsTo(Risk::class, 'risk_id', 'risk_id');
    }
}
