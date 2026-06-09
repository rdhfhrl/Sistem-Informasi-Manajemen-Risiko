<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskCategory extends Model
{
    use HasFactory;

    protected $table = 'risk_categories';
    protected $primaryKey = 'risk_category_id';
    
    protected $fillable = [
        'risk_category_name',
        'risk_category_description',
    ];

    // Relationships
    public function risks()
    {
        return $this->hasMany(Risk::class, 'risk_category_id', 'risk_category_id');
    }
}
