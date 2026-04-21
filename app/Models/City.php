<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['city_name'])]
class City extends Model
{
    use HasFactory;

    /**
     * City has many Colleges.
     */
    public function colleges(): HasMany
    {
        return $this->hasMany(College::class);
    }
}
