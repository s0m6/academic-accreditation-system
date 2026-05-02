<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommitteeApproval extends Model
{
    protected $fillable = [
        'report_id',
        'member_id',
        'iteration_number',
        'status',
        'review_round',
        'reject_reason',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'iteration_number' => 'integer',
    ];

    /**
     * Get the report associated with the approval.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(CommitteeReport::class, 'report_id');
    }

    /**
     * Get the evaluator (member) associated with the approval.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Evaluator::class, 'member_id');
    }

    /**
     * Get the signatures associated with the approval.
     */
    public function signatures(): HasMany
    {
        return $this->hasMany(ReportSignature::class, 'approval_id');
    }
}
