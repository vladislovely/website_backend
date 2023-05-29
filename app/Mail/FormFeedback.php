<?php

namespace App\Mail;

use App\Models\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FormFeedback extends Mailable
{
    use Queueable, SerializesModels;

    public Mail $mail;
    /**
     * Create a new message instance.
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from:    new Address($this->mail->from),
            subject: 'Form Feedback',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     * @throws \JsonException
     */
    public function attachments(): array
    {
        $files = json_decode($this->mail->attachments, true, 512, JSON_THROW_ON_ERROR);

        if (!empty($files)) {
            $data = [];

            foreach ($files as $file) {
                $data[] = Attachment::fromStorage($file['path'])
                                    ->as($file['name'])
                                    ->withMime($file['mime_type']);
            }

            return $data;
        }
        return [];
    }
}
