<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #333; margin-top: 0;">Verification Code</h2>
        
        <p>Hello,</p>
        
        <p>Your verification code is:</p>
        
        <div style="background-color: #fff; padding: 20px; border: 2px solid #007bff; border-radius: 5px; text-align: center; margin: 20px 0;">
            <h1 style="color: #007bff; font-size: 32px; margin: 0; letter-spacing: 5px;">{{ $code }}</h1>
        </div>
        
        <p>Please use this code to verify your identity. This code will expire in a few minutes.</p>
        
        <p>If you didn't request this code, please ignore this email.</p>
        
        <p style="margin-top: 30px;">
            Regards,<br>
            {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
