<?php

namespace App\Mail;

use App\Models\ContactInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public ContactInquiry $inquiry;
    public string $reply;

    public function __construct(ContactInquiry $inquiry, string $reply)
    {
        $this->inquiry = $inquiry;
        $this->reply = $reply;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Balasan Pesan - Johen Gaming',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-reply',
        );
    }
}
