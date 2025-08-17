<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Challans Ready</title>
</head>
<body>
    <h2>Hello {{ $user->name }},</h2>

    <p>Your license plate challans have been generated successfully.</p>

    <p>Click the links below to download your files:</p>

    <ul>
        @foreach($links as $link)
            <li><a href="{{ $link }}" target="_blank">{{ basename($link) }}</a></li>
        @endforeach
    </ul>
    
    
    <p>Total Plates Created: {{ $total }}</p>

    <p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>
