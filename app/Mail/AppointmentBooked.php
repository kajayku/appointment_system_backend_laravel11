<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentBooked extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $timezone;
    public $user;

    public function __construct(Appointment $appointment, $timezone,$user)
    {
         $this->appointment = $appointment;
         $this->timezone = $timezone;
         $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Booked',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-booked',
            with: [
                'user' => $this->user,
                'title' => $this->appointment->title,
                'description' => $this->appointment->description,
                'date' => Carbon::parse($this->appointment->appointment_date)->tz($this->timezone)->format('Y-m-d H:i:s'),
                'timezone' => $this->timezone,
                'description' => $this->appointment->description,
                'guests' => $this->appointment->guests->pluck('email')->toArray(),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

