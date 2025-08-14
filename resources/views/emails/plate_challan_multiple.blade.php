<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Your Number Plate Challans</title>
</head>

<body style="font-family:'Segoe UI', Tahoma, sans-serif; background-color:#f4f7fa; color:#333; margin:0; padding:0;">
    <div
        style="max-width:700px; margin:auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 3px 10px rgba(0,0,0,0.1);">
        <div style="background-color:#2a9d8f; padding:20px; color:white; text-align:center;">
            <h1 style="margin:0; font-size:22px;">Number Plate Challans</h1>
            <p>Due Date: <strong>{{ $dueDate }}</strong></p>
        </div>
        <div style="padding:25px;">
            <p>Dear {{ $user->name }},</p>
            <p>Thank you for registering your number plates. Please find your challans attached to this email.</p>
            @if (count($platesData) > 1)
                <p>You Have Created {{ count($platesData) }} Plates</p>
            @endif
            @foreach ($platesData as $item)
                @php
                    $plate = $item['plate'];
                    $provinceLogo = $item['provinceLogo'];
                @endphp
                @include('emails.plates', ['plate' => $plate, 'provinceLogo' => $provinceLogo])
                <hr style="border:none; border-top:1px solid #e5e7eb; margin:20px 0;">
            @endforeach

            <p>All challans are attached as PDF files to this email.</p>
        </div>
        <div style="text-align:center; font-size:12px; padding:15px; background:#f1f5f9; color:#6b7280;">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
