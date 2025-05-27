{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>تم استلام طلبك بنجاح</title>
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
            max-width: 600px;
            margin: auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }

        h2 {
            color: #333;
        }

        p {
            font-size: 16px;
            color: #555;
        }

        .order-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: right;
        }

        .order-items {
            margin-top: 20px;
        }

        .order-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .shipping-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: right;
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
        <div class="header">
            <h1>تم استلام طلبك بنجاح</h1>
        </div>

        <div class="order-details">
            <h2>تفاصيل الطلب</h2>
            <p><strong>رقم الطلب:</strong> #{{ $order->id }}</p>
            <p><strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
            <p><strong>المبلغ الإجمالي:</strong> {{ number_format($order->total_price, 2) }} جنيه</p>

            <div class="order-items">
                <h3>المنتجات</h3>
                @foreach ($order->orderItems as $item)
                    <div class="order-item">
                        <p><strong>{{ $item->product->title }}</strong></p>
                        <p>الكمية: {{ $item->quantity }}</p>
                        <p>السعر: {{ number_format($item->price, 2) }} جنيه</p>
                        @if ($item->product)
                            <p>الوصف: {{ $item->product->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="shipping-details">
            <h2>تفاصيل الشحن</h2>
            <p><strong>الاسم:</strong> {{ $user->full_name }}</p>
            <p><strong>البريد الإلكتروني:</strong> {{ $user->email }}</p>
            <p><strong>رقم الهاتف:</strong> {{ $user->phone_number }}</p>
            <p><strong>العنوان:</strong> {{ $user->full_address }}</p>
        </div>

        <div class="footer">
            <p>شكراً لاختيارك Mr. Mobiles</p>
            <p>لأي استفسارات، يرجى التواصل معنا على: support@mrmobiles.com</p>
        </div>
    </div>
</body>

</html> --}}
