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

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
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
                'title' => $this->appointment->title,
                'description' => $this->appointment->description,
                'date' => Carbon::parse($this->appointment->appointment_date)->format('Y-m-d H:i:s')
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
