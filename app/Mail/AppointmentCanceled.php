<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentCanceled extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment; 
    public $user;

    public function __construct(Appointment $appointment,$user)
    {
        $this->appointment = $appointment;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Canceled',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-canceled',
            with: [
                'name' => $this->user,
                'title' => $this->appointment->title,
                'date' => $this->appointment->appointment_date,
                'description' => $this->appointment->description,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
