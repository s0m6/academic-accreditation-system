<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    /** @use HasFactory<\Database\Factories\IndicatorFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'name',
        'sub_standard_id',
    ];

    /**
     * Get the sub standard that owns the indicator.
     */
    public function subStandard()
    {
        return $this->belongsTo(SubStandard::class);
    }

    /**
     * Get the evaluations for the indicator.
     */
    public function evaluations()
    {
        return $this->hasMany(IndicatorEvaluation::class);
    }

    /**
     * Get the evidences for the indicator.
     */
    public function evidences()
    {
        return $this->hasMany(Evidence::class);
    }
}
