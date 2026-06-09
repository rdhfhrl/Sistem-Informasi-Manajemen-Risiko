<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrategicObjective extends Model
{
    use HasFactory;

    protected $table = 'strategic_objectives';
    protected $primaryKey = 'strategic_objective_id';
    
    protected $fillable = [
        'strategic_objective_organization_id',
        'strategic_objective_name',
    ];

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }

    public function risks()
    {
        return $this->hasMany(Risk::class, 'risk_strategic_objective_id', 'strategic_objective_id');
    }
}
