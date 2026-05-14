<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FinalDecision extends Model
{
    use HasFactory;

    protected $fillable = [
        'accreditation_request_id',
        'issued_by',
        'decision_type',
        'notes',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    /**
     * Arabic labels and duration for each decision type.
     */
    public static array $decisionMeta = [
        'approved_achieved' => ['label' => 'محقق',          'approved' => true,  'years' => 3, 'followup' => 'متابعة الاعتماد بعد ثلاث سنوات'],
        'approved_with_mastery' => ['label' => 'محقق بإتقان',   'approved' => true,  'years' => 4, 'followup' => 'متابعة الاعتماد بعد أربع سنوات'],
        'approved_with_excellence' => ['label' => 'محقق بتميز',    'approved' => true,  'years' => 5, 'followup' => 'متابعة الاعتماد بعد خمس سنوات'],
        'rejected_partial' => ['label' => 'محقق جزئياً',   'approved' => false, 'years' => 0, 'followup' => 'يمنح مهلة سنة لإعادة التقدم'],
        'rejected_not_achieved' => ['label' => 'غير محقق',       'approved' => false, 'years' => 0, 'followup' => 'يمنح مهلة سنتين لإعادة التقدم'],
    ];

    /**
     * Get the accreditation request that owns the decision.
     */
    public function accreditationRequest(): BelongsTo
    {
        return $this->belongsTo(AccreditationRequest::class);
    }

    /**
     * Get the user who issued the decision.
     */
    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the certificate associated with this decision.
     */
    public function certificate(): HasOne
    {
        return $this->hasOne(AccreditationCertificate::class);
    }

    /**
     * Whether this decision is an approval.
     */
    public function isApproved(): bool
    {
        return self::$decisionMeta[$this->decision_type]['approved'] ?? false;
    }

    /**
     * Arabic label for this decision type.
     */
    public function decisionLabel(): string
    {
        return self::$decisionMeta[$this->decision_type]['label'] ?? $this->decision_type;
    }
}
