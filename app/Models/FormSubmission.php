<?php

namespace App\Models;

use Database\Factories\FormSubmissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    /** @use HasFactory<FormSubmissionFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'form_data' => 'array',
            'decision_reasons' => 'array',
            'submitted_at' => 'datetime',
            'decision_at' => 'datetime',
        ];
    }

    /**
     * Get the accreditation request this form belongs to.
     */
    public function accreditationRequest()
    {
        return $this->belongsTo(AccreditationRequest::class);
    }

    /**
     * Get the user who submitted this form.
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who decided on this form.
     */
    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
