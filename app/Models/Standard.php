<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    /** @use HasFactory<\Database\Factories\StandardFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'name',
        'description',
    ];

    /**
     * Get the sub standards for the standard.
     */
    public function subStandards()
    {
        return $this->hasMany(SubStandard::class);
    }
}
