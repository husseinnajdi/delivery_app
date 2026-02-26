<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(to right, #4CAF50, #81C784);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8em;
        }
        .body {
            padding: 30px;
            color: #333;
            line-height: 1.6;
        }
        .token-box {
            background: #f0f0f0;
            border: 1px dashed #aaa;
            border-radius: 8px;
            padding: 15px;
            font-size: 1em;
            color: #333;
            word-break: break-all;
            margin: 20px 0;
            text-align: center;
            letter-spacing: 1px;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: #4CAF50;
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 0.85em;
            color: #999;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🔒 Password Reset</h1>
    </div>

    <div class="body">
        <p>Hello,</p>
        <p>We received a request to reset your password. Click the button below or use the token to reset it:</p>

        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>
        </div>

        <p>Or copy this token manually:</p>
        <div class="token-box">{{ $token }}</div>

        <p>This link will expire in <strong>60 minutes</strong>.</p>
        <p>If you did not request a password reset, you can safely ignore this email.</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>

</body>
</html>