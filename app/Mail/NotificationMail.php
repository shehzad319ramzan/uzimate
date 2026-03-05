<?php

namespace App\Mail;

use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $mailSubject,
        public string $mailBody,
        public string $mailType = 'generic',
        public ?string $recipientName = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->mailSubject);
    }

    public function content(): Content
    {
        $setting = app(SettingService::class)->getSettings();

        return new Content(view: 'emails.notification', with: [
            'subject' => $this->mailSubject,
            'body' => $this->mailBody,
            'type' => $this->mailType,
            'recipientName' => $this->recipientName,
            'setting' => $setting,
            'appName' => config('app.name'),
            'appUrl' => config('app.url'),
        ]);
    }
}
