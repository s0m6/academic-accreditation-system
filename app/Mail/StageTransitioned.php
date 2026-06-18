<?php

namespace App\Mail;

use App\Models\AccreditationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StageTransitioned extends Mailable
{
    use Queueable, SerializesModels;

    // Initialize mailable properties
    public function __construct(
        public AccreditationRequest $accreditationRequest,
        public ?string $oldStage,
        public ?string $newStage,
        public bool $isFinalDecision = false
    ) {}

    // Get the message envelope with dynamic subject
    public function envelope(): Envelope
    {
        $programName = $this->accreditationRequest->program->program_name ?? 'البرنامج';
        $subject = $this->isFinalDecision
            ? "صدور القرار النهائي لطلب اعتماد برنامج ({$programName})"
            : "تحديث حالة طلب اعتماد برنامج ({$programName})";

        return new Envelope(
            subject: $subject,
        );
    }

    // Get the message content definition pointing to the Arabic email view
    public function content(): Content
    {
        return new Content(
            view: 'emails.stage_transitioned',
        );
    }

    // Get the attachments for the message
    public function attachments(): array
    {
        return [];
    }
}
