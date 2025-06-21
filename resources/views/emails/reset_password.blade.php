<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Your Password</title>
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
            color: #dc3545;
        }

        p {
            font-size: 16px;
            color: #555;
        }

        .reset-button {
            display: inline-block;
            background: #28a745;
            color: #fff !important;
            text-decoration: none;
            padding: 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin: 15px 0;
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
        <h2>🔒 Reset Your Password</h2>
        <p>Hello, <strong>{{ $user->name }}</strong> 👋</p>
        <p>We received a request to reset your password. Click the button below to set a new one:</p>
        <a href="{{ url('http://localhost:3007/resetPassword?token=' . $token) }}" class="reset-button">🔑 Reset
            Password</a>
        <p>🔹 This link will expire in <strong>1 hour</strong> for your security.</p>
        <p>If you did not request this reset, you can safely ignore this email. Your password will remain unchanged.</p>
        <div class="footer">
            <p>📞 Need help? Contact our support team.</p>
            <p>🛠️ <strong>The Mr-Mobiles Team</strong></p>
        </div>
    </div>
</body>

</html>
