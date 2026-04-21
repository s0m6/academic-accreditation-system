<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $fillable = [
        'accreditation_request_id',
        'status',
        'chair_evaluator_id',
    ];

    public function accreditationRequest()
    {
        return $this->belongsTo(AccreditationRequest::class);
    }

    public function chairEvaluator()
    {
        return $this->belongsTo(Evaluator::class, 'chair_evaluator_id');
    }
}
