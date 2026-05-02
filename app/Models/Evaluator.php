<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'city_id',
        'general_specialty',
        'detailed_specialty',
        'academic_rank',
        'current_university_id',
    ];

    /**
     * Get the user that owns the evaluator profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the city where the evaluator is located.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the university where the evaluator currently works.
     */
    public function currentUniversity(): BelongsTo
    {
        return $this->belongsTo(University::class, 'current_university_id');
    }

    /**
     * Get the conflicts of interest for the evaluator.
     */
    public function conflicts(): HasMany
    {
        return $this->hasMany(EvaluatorConflict::class);
    }

    /**
     * Get the attachments for the evaluator.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(EvaluatorAttachment::class);
    }

    /**
     * Get the committee memberships for the evaluator.
     */
    public function committeeMemberships(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }

    /**
     * Get the approvals made by the evaluator.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(CommitteeApproval::class, 'member_id');
    }
}
