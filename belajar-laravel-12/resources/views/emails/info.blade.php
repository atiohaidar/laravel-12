<!DOCTYPE html>
<html>
<head>
    <title>Information</title>
</head>
<body>
    <h2>Hello {{ $user->name }},</h2>
    
    <div style="margin: 20px 0;">
        {!! nl2br(e($messageContent)) !!}
    </div>

    <p>Best regards,<br>{{ config('app.name') }}</p>
</body>
</html>
