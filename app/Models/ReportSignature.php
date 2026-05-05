<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSignature extends Model
{
    protected $fillable = [
        'report_id',
        'approval_id',
        'form_type',
        'signature_path',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the report associated with the signature.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(CommitteeReport::class, 'report_id');
    }

    /**
     * Get the approval associated with the signature.
     */
    public function approval(): BelongsTo
    {
        return $this->belongsTo(CommitteeApproval::class, 'approval_id');
    }
}
