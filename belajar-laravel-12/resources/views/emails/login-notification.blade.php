<!DOCTYPE html>
<html>
<head>
    <title>Login Notification</title>
</head>
<body>
    <h2>Login Notification</h2>
    <p>Hello {{ $user->name }},</p>
    <p>Your account was successfully logged in at {{ $loginTime }}.</p>
    <p>If this wasn't you, please contact our support team immediately.</p>
    <br>
    <p>Best regards,</p>
    <p>{{ config('app.name') }}</p>
</body>
</html>
