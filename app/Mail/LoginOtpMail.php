<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $otp,
        public readonly string $schoolName,
        public readonly int    $expiryMinutes = 10,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your {$this->schoolName} Login OTP",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.login-otp',
        );
    }
}
