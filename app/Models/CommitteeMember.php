<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'committee_id',
        'evaluator_id',
        'member_status',
        'invite_sent_at',
        'member_responded_at',
        'university_responded_at',
        'reject_reason',
        'is_active',
    ];

    protected $casts = [
        'invite_sent_at' => 'datetime',
        'member_responded_at' => 'datetime',
        'university_responded_at' => 'datetime',
        'reject_reason' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the committee that the member belongs to.
     */
    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    /**
     * Get the evaluator associated with the committee member.
     */
    public function evaluator()
    {
        return $this->belongsTo(Evaluator::class);
    }
}
