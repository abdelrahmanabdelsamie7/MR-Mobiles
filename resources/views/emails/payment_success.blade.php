<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Successful</title>
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
            color: #28a745;
        }

        p {
            font-size: 16px;
            color: #555;
        }

        .order-info {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
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
        <h2>✅ Payment Successful</h2>
        <p>Thank you for your payment!</p>
        <div class="order-info">
            <p>Your payment of <strong>{{ $amount }} EGP</strong> has been received successfully.</p>
            <p>Order ID: <strong>{{ $cartId }}</strong></p>
        </div>
        <p>We appreciate your business! 😊</p>
        <div class="footer">
            <p>If you have any questions, feel free to contact our support team.</p>
            <p>📩 <strong>The Payment Team</strong></p>
        </div>
    </div>
</body>

</html>
