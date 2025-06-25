<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $announcement;
    public $custom_subject;
    public $custom_intro;

    /**
     * Create a new message instance.
     */
    public function __construct($announcement, $custom_subject = null, $custom_intro = null)
    {
        $this->announcement = $announcement;
        $this->custom_subject = $custom_subject;
        $this->custom_intro = $custom_intro;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Announcement Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $subject = $this->custom_subject ?: '[eBaryo] ' . $this->announcement->title;
        return new Content(
            markdown: 'emails.announcement',
            with: [
                'announcement' => $this->announcement,
                'custom_intro' => $this->custom_intro,
            ],
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
