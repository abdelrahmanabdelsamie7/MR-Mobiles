<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }

        .email-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }

        h2 {
            color: #333;
        }

        p {
            font-size: 16px;
            color: #555;
        }

        .btn {
            display: inline-block;
            background-color: #28a745;
            color: #fff !important;
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 18px;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>👋 Hello, {{ $userName }}!</h2>
        <p>Thank you for registering with <strong>Mr-Mobiles</strong> 🎉</p>
        <p>Please verify your email by clicking the button below. <br />
            🔹 This link is valid for only <strong>30 minutes</strong>.
        </p>
        <a href="{{ $verificationUrl }}" class="btn">✅ Verify Email</a>
        <p>If you did not create this account, please ignore this email.</p>
        <div class="footer">
            <p>💡 Need help? Contact our support team anytime.</p>
            <p>Thanks, <br />
                📱 <strong>The Mr-Mobiles Team</strong>
            </p>
        </div>
    </div>
</body>
</html>
