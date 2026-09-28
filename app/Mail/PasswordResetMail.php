<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    // ==================== Constructor ====================

    public function __construct(
        public string $resetUrl
    ) {
        //
    }

    // ==================== Email Subject ====================

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your DELIVER Password',
        );
    }

    // ==================== Email Content ====================

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
        );
    }
}
