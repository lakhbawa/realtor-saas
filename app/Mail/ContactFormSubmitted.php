<?php

namespace App\Mail;

use App\Models\ContactSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactSubmission $submission
    ) {}

    public function envelope(): Envelope
    {
        $subject = 'New Contact Form Submission';

        if ($this->submission->property) {
            $subject .= ' - ' . $this->submission->property->title;
        }

        return new Envelope(
            subject: $subject,
            replyTo: [
                $this->submission->email,
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-submitted',
            with: [
                'submission' => $this->submission,
                'property' => $this->submission->property,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
