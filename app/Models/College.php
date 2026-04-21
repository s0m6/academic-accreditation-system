<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'university_id',
    'city_id',
    'dean_name',
    'dean_email',
    'dean_mobile',
    'dean_phone',
])]
class College extends Model
{
    use HasFactory;

    /**
     * College belongs to a University.
     */
    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    /**
     * College belongs to a City.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * College has many Departments.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}
