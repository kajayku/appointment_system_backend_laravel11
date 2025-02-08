<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Guest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentBooked;
use App\Mail\AppointmentCanceled;
use App\Jobs\SendReminderEmail;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{


    public function viewAllAppointments(Request $request)

        {
            $user = Auth::user();
            
            $query = Appointment::where('user_id', $user->id);

            if ($request->has('sort_by')) {
                if ($request->sort_by === 'created_date') {
                    $query->orderBy('created_at', 'desc'); 
                } elseif ($request->sort_by === 'upcoming') {
                    $query->where('appointment_date', '>', Carbon::now())
                        ->orderBy('appointment_date', 'asc'); 
                }
            }
            $appointments = $query->with('guests')->paginate(10); 

            return response()->json([
                'appointments' => $appointments
            ], 200);
        }



    public function bookAppointment(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'appointment_date' => 'required',
            'timezone' => 'required|timezone',
            'reminder_time' => 'nullable|integer|min:5|max:1440', 
            'guests' => 'nullable|array',
            'guests.*' => 'email'
        ]);


        // Convert the local appointment date to UTC
    $appointmentDateUtc = Carbon::parse($validated['appointment_date'], $validated['timezone'])->utc();

    // Ensure the appointment is on a weekday
    if ($appointmentDateUtc->isWeekend()) {
        return response()->json([
            'message' => 'Appointments can only be booked on weekdays (Monday to Friday).',
        ], 401);    }


        $reminderTime = $validated['reminder_time'] ?? 30;

        $appointment = Appointment::create([
            'title' => $validated['title'],
            'user_id' => 1,
            'description' => $validated['description'],
            'appointment_date' => $validated['appointment_date'],
            'status' => 'Booked',
            'reminder_time' => $reminderTime,
        ]);

        if (!empty($validated['guests'])) {
            foreach ($validated['guests'] as $email) {
                Guest::create([
                    'appointment_id' => $appointment->id,
                    'email' => $email
                ]);
            }
        }
         $user = Auth::user()->name;
        // Send Email Notifications
        $allRecipients = array_merge([$request->user()->email], $validated['guests'] ?? []);
        Mail::to($request->user()->email)->cc($validated['guests'] ?? [])->send(new AppointmentBooked($appointment, $validated['timezone'],$user));


        // Dispatch reminder email job
         $reminderTimeUtc = $appointmentDateUtc->subMinutes($reminderTime);
         SendReminderEmail::dispatch($appointment, $validated['timezone'],$user)->delay($reminderTimeUtc);

        return response()->json([
            'message' => 'Appointment booked successfully',
            'appointment' => $appointment,
            'guests' => $validated['guests'] ?? []
        ], 201);
    }



    public function cancelAppointment(Request $request, $appointmentId)
{
    $appointment = Appointment::findOrFail($appointmentId);

    // if ($appointment->user_id !== $request->user()->id) {
    //     return response()->json(['message' => 'Unauthorized'], 403);
    // }

    $now = Carbon::now();
    $appointmentTime = Carbon::parse($appointment->appointment_date);

    if ($now->diffInMinutes($appointmentTime, false) < 30) {
        return response()->json(['message' => 'You can only cancel an appointment at least 30 minutes before the scheduled time.'], 400);
    }

    $appointment->update(['status' => 'canceled']);
    $user = Auth::user()->name;
    $recipients = array_merge([$request->user()->email], $appointment->guests->pluck('email')->toArray());

    Mail::to($request->user()->email)->cc($appointment->guests->pluck('email'))->send(new AppointmentCanceled($appointment,$user));

    return response()->json(['message' => 'Appointment canceled successfully.'], 200);
}


}
