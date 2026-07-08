<?php

namespace App\Mail;

use App\Models\Entry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EntryConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Entry $entry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '【受付完了】' . $this->entry->event->title . ' 受付番号：' . $this->entry->entry_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.entry_confirmation',
        );
    }
}
