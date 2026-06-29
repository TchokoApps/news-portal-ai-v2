<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 5px 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>

        <div class="content">
            <p>Hello,</p>

            <p>You have requested to reset your admin password. Please click the button below to proceed:</p>

            <div style="text-align: center;">
                <a href="{{ url(route('admin.reset-password.create', $token, false) . '?email=' . urlencode($email)) }}" class="button">
                    Reset Password
                </a>
            </div>

            <p>Or copy and paste this link in your browser:</p>
            <p style="word-break: break-all; color: #666;">
                {{ url(route('admin.reset-password.create', $token, false) . '?email=' . urlencode($email)) }}
            </p>

            <p style="color: #999; font-size: 12px;">
                This link will expire in 24 hours.
            </p>

            <p>If you did not request a password reset, please ignore this email.</p>

            <p>Best regards,<br>News Portal AI Team</p>
        </div>

        <div class="footer">
            <p>&copy; {{ now()->year }} News Portal AI. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
