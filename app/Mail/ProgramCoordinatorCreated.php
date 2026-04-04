<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProgramCoordinatorCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $password,
        public string $verificationUrl,
        public string $programName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'دعوة منصة الاعتماد - حساب منسق البرنامج',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.program_coordinator_created',
        );
    }
}
