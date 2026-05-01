<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Appointment  $appointment  the cancelled appointment
     * @param  string       $cancelledBy  'patient' or 'doctor' — who initiated the cancellation
     */
    public function __construct(
        public Appointment $appointment,
        public string $cancelledBy
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment cancelled',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointments.cancelled',
        );
    }
}
