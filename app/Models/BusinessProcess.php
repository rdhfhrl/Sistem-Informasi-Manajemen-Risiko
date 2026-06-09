<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProcess extends Model
{
    use HasFactory;

    protected $table = 'business_processes';
    protected $primaryKey = 'business_process_id';
    
    protected $fillable = [
        'business_process_organization_id',
        'business_process_name',
        'business_process_description',
    ];

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }

    public function risks()
    {
        return $this->hasMany(Risk::class, 'risk_business_process_id', 'business_process_id');
    }
}
