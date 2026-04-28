<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitSchedule extends Model
{
    protected $fillable = [
        'accreditation_request_id',
        'committee_id',
        'status',
        'schedule_data',
        'council_pdf_path',
        'rejection_reason',
        'submitted_at',
        'council_processed_at',
        'university_responded_at',
    ];

    protected $casts = [
        'schedule_data' => 'array',
        'rejection_reason' => 'array',
        'submitted_at' => 'datetime',
        'council_processed_at' => 'datetime',
        'university_responded_at' => 'datetime',
    ];

    /**
     * Get the accreditation request associated with this visit schedule.
     */
    public function accreditationRequest()
    {
        return $this->belongsTo(AccreditationRequest::class);
    }

    /**
     * Get the committee associated with this visit schedule.
     */
    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }
}
