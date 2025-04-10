<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignFinalizedNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     * Constructor for the email notification.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     * Defines the envelope for the email, including the subject.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Campaign Finalized Notification', // Subject of the email
        );
    }

    /**
     * Get the message content definition.
     * Defines the content view for the email.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.campaign_finalized', // The view file used for the email
        );
    }

    /**
     * Get the attachments for the message.
     * Returns any attachments for the email. This method can be expanded to include file attachments.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return []; // No attachments by default
    }
}
