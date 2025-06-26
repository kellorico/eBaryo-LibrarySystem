<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ReadingChallengeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $challenge;
    public $custom_subject;
    public $custom_intro;

    /**
     * Create a new message instance.
     */
    public function __construct($challenge, $custom_subject = null, $custom_intro = null)
    {
        $this->challenge = $challenge;
        $this->custom_subject = $custom_subject;
        $this->custom_intro = $custom_intro;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->custom_subject ?: '[eBaryo] New Reading Challenge: ' . $this->challenge->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reading_challenge',
            with: [
                'challenge' => $this->challenge,
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
