<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuggestionResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $suggestion;
    public $response;

    public function __construct($suggestion, $response)
    {
        $this->suggestion = $suggestion;
        $this->response = $response;
    }

    public function build()
    {
        return $this->subject('[eBaryo] Response to Your Suggestion')
            ->markdown('emails.suggestion_response', [
                'suggestion' => $this->suggestion,
                'response' => $this->response,
            ]);
    }
} 