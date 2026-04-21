<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'program_name',
    'degree_level',
    'program_details',
    'department_id',
])]
class Program extends Model
{
    use HasFactory;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'program_details' => 'array',
        ];
    }

    /**
     * Program belongs to a Department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Program has many AccreditationRequests.
     */
    public function accreditationRequests(): HasMany
    {
        return $this->hasMany(AccreditationRequest::class);
    }

    /**
     * Get the latest accreditation request for this program.
     */
    public function latestAccreditationRequest(): HasOne
    {
        return $this->hasOne(AccreditationRequest::class)->latestOfMany();
    }
}
