<?php

namespace App\Models;

use Database\Factories\IndicatorEvaluationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicatorEvaluation extends Model
{
    /** @use HasFactory<IndicatorEvaluationFactory> */
    use HasFactory;

    protected $fillable = [
        'form_submission_id',
        'indicator_id',
        'score',
    ];

    /**
     * Get the form submission that owns the evaluation.
     */
    public function formSubmission()
    {
        return $this->belongsTo(FormSubmission::class);
    }

    /**
     * Get the indicator that is being evaluated.
     */
    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    /**
     * Get the evidences for the indicator evaluation.
     */
    public function evidences()
    {
        return $this->hasMany(Evidence::class);
    }
}
