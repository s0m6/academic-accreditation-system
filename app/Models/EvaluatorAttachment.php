<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluatorAttachment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'evaluator_id',
        'name',
        'path',
    ];

    /**
     * Get the evaluator that owns the attachment.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(Evaluator::class);
    }
}
