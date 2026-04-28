<?php

namespace App\Models;

use Database\Factories\AccreditationRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationRequest extends Model
{
    /** @use HasFactory<AccreditationRequestFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the program that owns the accreditation request.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the council coordinator assigned to this request.
     */
    public function councilCoordinator()
    {
        return $this->belongsTo(User::class, 'council_coord_id');
    }

    /**
     * Get the program coordinator who created/owns this request.
     */
    public function programCoordinator()
    {
        return $this->belongsTo(User::class, 'program_coord_id');
    }

    /**
     * Get the form submissions for this request.
     */
    public function formSubmissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    /**
     * Get the evaluation committee for this request.
     */
    public function committee()
    {
        return $this->hasOne(Committee::class);
    }

    /**
     * Get the visit schedules for this request (multiple versions allowed).
     */
    public function visitSchedules()
    {
        return $this->hasMany(VisitSchedule::class);
    }
}
