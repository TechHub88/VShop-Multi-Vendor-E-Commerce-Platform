<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification OTP</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6fc;
            font-family: 'Outfit', 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f6fc;
            padding: 40px 0;
        }
        .container {
            width: 100%;
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            padding: 40px 40px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 8px 0 0;
            color: #bfdbfe;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .content {
            padding: 40px;
        }
        .content p {
            margin: 0 0 16px;
            font-size: 16px;
            line-height: 1.6;
            color: #475569;
        }
        .content p.greeting {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 20px;
        }
        .otp-container {
            text-align: center;
            margin: 32px 0;
        }
        .otp-box {
            display: inline-block;
            background: #eff6ff;
            border: 2px dashed #3b82f6;
            border-radius: 12px;
            padding: 24px 48px;
        }
        .otp-label {
            margin: 0;
            font-size: 11px;
            font-weight: 700;
            color: #1e3a8a;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .otp-code {
            margin: 12px 0 0;
            font-size: 48px;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: 12px;
        }
        .notice {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 32px;
        }
        .notice p {
            margin: 0;
            color: #b45309;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.5;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.5;
        }
        .footer a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }
        .footer p.copyright {
            margin-bottom: 6px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <h1>vridhee</h1>
                <p>Email Verification OTP</p>
            </div>
            
            <!-- Content -->
            <div class="content">
                <p class="greeting">Hello {{ $sellerName }},</p>
                <p>We received a request to verify your email address for your Seller Profile on VSHOP. Please use the following One-Time Password (OTP) to complete the verification process:</p>
                
                <!-- OTP Section -->
                <div class="otp-container">
                    <div class="otp-box">
                        <p class="otp-label">Verification Code</p>
                        <p class="otp-code">{{ $otp }}</p>
                    </div>
                </div>
                
                <!-- Notice -->
                <div class="notice">
                    <p>⏱️ This OTP is valid for <strong>10 minutes</strong>. For security reasons, please do not share this code with anyone.</p>
                </div>
                
                <p>If you did not initiate this request, you can safely ignore this email. Your profile remains secure.</p>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <p class="copyright">&copy; {{ date('Y') }} VRIDHEE INNOVATIONS PRIVATE LIMITED. All rights reserved.</p>
                <p>VSHOP Seller Portal &bull; <a href="https://vshop.zelofx.com/seller">vshop.zelofx.com/seller</a></p>
            </div>
        </div>
    </div>
</body>
</html>
