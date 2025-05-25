<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PadApplication;

class ApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $pad;
    public $tenant;

    /**
     * Create a new message instance.
     */
    public function __construct(PadApplication $application)
    {
        $this->application = $application;
        $this->pad = $application->pad;
        $this->tenant = $application->tenant;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Pad Application Received - ' . $this->pad->padName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application-received',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
