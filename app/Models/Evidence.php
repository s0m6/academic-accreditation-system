<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    /** @use HasFactory<\Database\Factories\EvidenceFactory> */
    use HasFactory;

    protected $table = 'evidences';

    protected $fillable = [
        'form_submission_id',
        'indicator_id',
        'file_name',
        'file_path',
    ];

    /**
     * Get the form submission that owns the evidence.
     */
    public function formSubmission()
    {
        return $this->belongsTo(FormSubmission::class);
    }

    /**
     * Get the indicator associated with the evidence.
     */
    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }
}
