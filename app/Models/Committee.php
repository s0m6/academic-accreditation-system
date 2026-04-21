<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = [
        'accreditation_request_id',
        'status',
        'chair_evaluator_id',
    ];

    /**
     * Get the accreditation request owning this committee.
     */
    public function accreditationRequest()
    {
        return $this->belongsTo(AccreditationRequest::class);
    }

    /**
     * Get the chair evaluator for this committee.
     */
    public function chairEvaluator()
    {
        return $this->belongsTo(Evaluator::class, 'chair_evaluator_id');
    }

    /**
     * Get all members for the committee.
     */
    public function members()
    {
        return $this->hasMany(CommitteeMember::class);
    }

    /**
     * Get only the currently active members (visible on the UI cards).
     */
    public function activeMembers()
    {
        return $this->hasMany(CommitteeMember::class)->where('is_active', true);
    }

    /**
     * Get only members with fully accepted status (used to gate committee approval).
     */
    public function acceptedMembers()
    {
        return $this->hasMany(CommitteeMember::class)
            ->where('is_active', true)
            ->where('member_status', 'accepted');
    }
}
