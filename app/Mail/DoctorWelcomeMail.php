<?php

namespace App\Mail;

use App\Models\Doctor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DoctorWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Doctor  $doctor         the newly-created doctor
     * @param  string  $tempPassword   plaintext temp password (sent once, never stored elsewhere)
     */
    public function __construct(
        public Doctor $doctor,
        public string $tempPassword
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your hospital doctor account has been created',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.doctors.welcome',
        );
    }
}
