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
            color: #555555;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h2>👋 Hello, {{ $name }}!</h2>
        <p style="font-weight: 600 ; color: #0a7543;">{{ $replyMessage }}</p>
        <p>Best regards , </p>
        <p> <strong>Mr-Mobiles Admin</strong> </p>
    </div>
    </div>
</body>

</html>
