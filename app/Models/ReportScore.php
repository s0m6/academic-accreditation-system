<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportScore extends Model
{
    protected $fillable = [
        'report_id',
        'indicator_id',
        'score',
        'score_type',
    ];

    /**
     * Get the report that owns the score.
     */
    public function report()
    {
        return $this->belongsTo(CommitteeReport::class, 'report_id');
    }

    /**
     * Get the indicator associated with the score.
     */
    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }
}
