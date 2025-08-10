<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">

    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 6px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">

        <h2 style="color: #333;">Password Reset Request</h2>

        <p>Hello,</p>

        <p>You recently requested to reset your password. Click the button below to proceed:</p>

        <p style="text-align: center;">
            <a href="{{ url('/password/reset/' . $token) }}"
               style="display: inline-block; padding: 12px 24px; background-color: #3490dc; color: #ffffff; text-decoration: none; border-radius: 4px;">
                Reset Password
            </a>
        </p>

        <p>If the button doesn't work, copy and paste the following URL into your browser:</p>

        <p style="word-break: break-all;">
            {{ url('/password/reset/' . $token ) }}
        </p>

        <p>This password reset link will expire in 60 minutes.</p>

        <p>If you did not request a password reset, no further action is required.</p>

        <p>Regards,<br>{{ config('app.name') }}</p>

    </div>
</body>
</html>
