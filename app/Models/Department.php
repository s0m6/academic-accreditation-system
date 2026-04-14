<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'college_id',
    'head_name',
    'head_email',
    'head_mobile',
    'head_phone',
])]
class Department extends Model
{
    /**
     * Department belongs to a College.
     */
    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    /**
     * Department has many Programs.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }
}
