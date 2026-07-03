<!DOCTYPE html>
<html>
<head>
    <title>Admin Staff Login OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <h2 style="color: #003a80; text-align: center;">Admin Verification</h2>
        <p style="font-size: 16px; color: #333;">Hello,</p>
        <p style="font-size: 16px; color: #333;">You are attempting to log in to the admin panel. Please use the following OTP to complete your login process:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <span style="display: inline-block; padding: 15px 30px; font-size: 24px; font-weight: bold; color: #003a80; background-color: #f0f8ff; border-radius: 8px; letter-spacing: 5px;">
                {{ $otp }}
            </span>
        </div>
        
        <p style="font-size: 14px; color: #666;">This OTP is valid for 15 minutes. Do not share this code with anyone.</p>
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #999; text-align: center;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
