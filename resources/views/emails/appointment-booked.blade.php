<!DOCTYPE html>
<html>
<head>
    <title>Appointment Confirmation</title>
</head>
<body>
    <p>Dear {{ $user }},</p>

    <p>Your appointment titled "<strong>{{ $title }}</strong>" has been successfully booked for <strong>{{ $date }}</strong> ({{ $timezone }}).</p>

    <p><strong>Description:</strong> {{ $description }}</p>

    <p><strong>Guests:</strong></p>
    <ul>
        @foreach($guests as $guest)
            <li>{{ $guest }}</li>
        @endforeach
    </ul>

    <p>Thank you!</p>
</body>
</html>

