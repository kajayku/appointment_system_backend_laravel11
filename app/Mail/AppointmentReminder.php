<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $timezone;

    public function __construct(Appointment $appointment, $timezone)
    {
        $this->appointment = $appointment;
        $this->timezone = $timezone;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Reminder',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-reminder',
            with: [
                'name' => $this->appointment->user->name,
                'title' => $this->appointment->title,
                'description' => $this->appointment->description,
                'date' => Carbon::parse($this->appointment->appointment_date)->tz($this->timezone)->format('Y-m-d H:i:s'),
                'timezone' => $this->appointment->timezone,
                ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
