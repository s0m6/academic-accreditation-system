<?php

namespace App\Models;

use Database\Factories\SubStandardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubStandard extends Model
{
    /** @use HasFactory<SubStandardFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'name',
        'standard_id',
        'examples_of_evidence',
    ];

    protected $casts = [
        'examples_of_evidence' => 'json',
    ];

    /**
     * Get the standard that owns the sub standard.
     */
    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    /**
     * Get the indicators for the sub standard.
     */
    public function indicators()
    {
        return $this->hasMany(Indicator::class);
    }
}
