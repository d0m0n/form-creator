<?php

namespace App\Mail;

use App\Models\GuestUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public GuestUser $guest,
        public string $token
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【メール認証】イベントフォーム作成の確認',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.guest_email_verification',
            with: [
                'verifyUrl' => route('guest.email.verify', $this->token),
            ],
        );
    }
}
