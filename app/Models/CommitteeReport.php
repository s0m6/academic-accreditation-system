<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommitteeReport extends Model
{
    protected $fillable = [
        'accreditation_request_id',
        'status',
        'current_iteration',
        'form5_data',
        'form6_initial_data',
        'stage6_submitted_at',
        'form8_pdf_path',
        'council_responded_at',
        'form9_data',
        'form9_pdf_path',
        'uni_responded_at',
        'form6_final_data',
        'form10_pdf_path',
        'stage8_submitted_at',
    ];

    protected $casts = [
        'form5_data' => 'array',
        'form6_initial_data' => 'array',
        'form9_data' => 'array',
        'form6_final_data' => 'array',
        'stage6_submitted_at' => 'datetime',
        'council_responded_at' => 'datetime',
        'uni_responded_at' => 'datetime',
        'stage8_submitted_at' => 'datetime',
    ];

    /**
     * Get the accreditation request that owns the report.
     */
    public function accreditationRequest()
    {
        return $this->belongsTo(AccreditationRequest::class);
    }

    /**
     * Get the scores for this report.
     */
    public function scores()
    {
        return $this->hasMany(ReportScore::class, 'report_id');
    }
}
