<?php

namespace App\Models;

use App\Mail\StageTransitioned;
use Database\Factories\AccreditationRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class AccreditationRequest extends Model
{
    /** @use HasFactory<AccreditationRequestFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    // Boot the model and register updated event listener
    protected static function booted(): void
    {
        static::updated(function (AccreditationRequest $accreditationRequest) {
            // Send email if current_stage transitions to stage_two or higher
            if ($accreditationRequest->isDirty('current_stage')) {
                $oldStage = $accreditationRequest->getOriginal('current_stage');
                $newStage = $accreditationRequest->current_stage;

                $stages = ['stage_two', 'stage_three', 'stage_four', 'stage_five', 'stage_six', 'stage_seven', 'stage_eight', 'stage_nine'];
                if (in_array($newStage, $stages)) {
                    static::sendTransitionEmailNotification($accreditationRequest, $oldStage, $newStage, false);
                }
            }

            // Send email if the request is completed (final decision issued)
            if ($accreditationRequest->isDirty('request_status') && $accreditationRequest->request_status === 'completed') {
                static::sendTransitionEmailNotification($accreditationRequest, null, null, true);
            }
        });
    }

    // Helper method to resolve recipients and send stage transition email
    protected static function sendTransitionEmailNotification(AccreditationRequest $accreditationRequest, ?string $oldStage, ?string $newStage, bool $isFinalDecision): void
    {
        // Load relationships fresh to avoid any cached stale states
        $accreditationRequest->load([
            'program.department.college.university.officer',
            'programCoordinator',
        ]);

        $coordinator = $accreditationRequest->programCoordinator;
        $officer = $accreditationRequest->program?->department?->college?->university?->officer;

        $recipients = [];
        if ($coordinator && $coordinator->email) {
            $recipients[] = $coordinator->email;
        }
        if ($officer && $officer->email) {
            $recipients[] = $officer->email;
        }

        if (! empty($recipients)) {
            Mail::to($recipients)->send(
                new StageTransitioned($accreditationRequest, $oldStage, $newStage, $isFinalDecision)
            );
        }
    }

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

    /**
     * Get the committee report for this request.
     */
    public function committeeReport()
    {
        return $this->hasOne(CommitteeReport::class);
    }

    /**
     * Get the final decision for this request.
     */
    public function finalDecision()
    {
        return $this->hasOne(FinalDecision::class);
    }
}
