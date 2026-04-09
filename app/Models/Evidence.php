<?php

namespace App\Models;

use Database\Factories\EvidenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    /** @use HasFactory<EvidenceFactory> */
    use HasFactory;

    protected $table = 'evidences';

    protected $fillable = [
        'indicator_evaluation_id',
        'file_name',
        'file_path',
    ];

    /**
     * Get the indicator evaluation that owns the evidence.
     */
    public function indicatorEvaluation()
    {
        return $this->belongsTo(IndicatorEvaluation::class);
    }
}
