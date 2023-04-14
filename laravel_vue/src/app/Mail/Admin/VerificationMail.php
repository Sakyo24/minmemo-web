<?php

declare(strict_types=1);

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string $url
     */
    public string $url;

    /**
     * @var string $password
     */
    public string $password;

    /**
     * Create a new message instance.
     *
     * @param string $url
     * @param string $password
     * @return void
     */
    public function __construct(string $url, string $password)
    {
        $this->url = $url;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '管理画面への招待',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.admin.verification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
