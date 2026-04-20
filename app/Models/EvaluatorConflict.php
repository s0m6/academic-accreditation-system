<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluatorConflict extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'evaluator_id',
        'university_id',
        'conflict_text',
    ];

    /**
     * Get the evaluator that owns the conflict.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(Evaluator::class);
    }

    /**
     * Get the university involved in the conflict.
     */
    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
