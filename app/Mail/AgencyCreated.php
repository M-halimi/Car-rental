<?php

namespace App\Mail;

use App\Models\Agency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgencyCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Agency $agency,
        public string $email,
        public string $password,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your CarRental.ma Agency Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.agency-created',
            with: [
                'agencyName' => $this->agency->name,
                'dashboardUrl' => url('/agency'),
                'email' => $this->email,
                'password' => $this->password,
            ],
        );
    }
}
