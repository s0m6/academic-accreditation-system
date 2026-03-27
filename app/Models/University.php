<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable([
    'name', 
    'type', 
    'city_id',
    'accreditation_officer_id', 
    'president_name', 
    'president_email', 
    'president_mobile', 
    'president_phone'
])]
class University extends Model
{
    /**
     * علاقة: الجامعة تتبع لمسؤول اعتماد واحد (User)
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accreditation_officer_id');
    }

    /**
     * University has many Colleges.
     */
    public function colleges(): HasMany
    {
        return $this->hasMany(College::class);
    }
}