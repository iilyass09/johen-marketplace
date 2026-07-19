<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $name;
    public string $type;

    public function __construct(string $otp, string $name, string $type = 'register')
    {
        $this->otp = $otp;
        $this->name = $name;
        $this->type = $type;
    }

    public function envelope(): Envelope
    {
        $subject = $this->type === 'password_reset'
            ? 'Kode OTP Reset Password - Johen Gaming'
            : 'Kode OTP Registrasi - Johen Gaming';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }
}
