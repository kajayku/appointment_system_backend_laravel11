<?php

namespace App\Jobs;

use App\Mail\AppointmentReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment;

class SendReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $appointment;
    public $timezone;

    public function __construct(Appointment $appointment, $timezone)
    {
        $this->appointment = $appointment;
        $this->timezone = $timezone;
    }

    public function handle()
    {
        $recipients = array_merge([$this->appointment->user->email], $this->appointment->guests->pluck('email')->toArray());
        Mail::to($this->appointment->user->email)->cc($this->appointment->guests->pluck('email'))->send(new AppointmentReminder($this->appointment, $this->timezone));
    }
}
