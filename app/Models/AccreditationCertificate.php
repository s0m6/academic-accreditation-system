<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccreditationCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'final_decision_id',
        'certificate_number',
        'certificate_data',
        'is_active',
    ];

    protected $casts = [
        'certificate_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the final decision that owns the certificate.
     */
    public function finalDecision(): BelongsTo
    {
        return $this->belongsTo(FinalDecision::class);
    }

    /**
     * Check whether the certificate is still valid (not expired).
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $expiresAt = $this->certificate_data['expires_at_raw'] ?? null;

        if (! $expiresAt) {
            return $this->is_active;
        }

        return now()->lessThanOrEqualTo($expiresAt);
    }
}
