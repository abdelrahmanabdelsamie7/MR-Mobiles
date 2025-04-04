<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>تم استلام طلبك بنجاح</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }

        .order-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .order-items {
            margin-top: 20px;
        }

        .order-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Mr. Mobiles Logo" class="logo">
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
                    <p><strong>{{ $item->product->name }}</strong></p>
                    <p>الكمية: {{ $item->quantity }}</p>
                    <p>السعر: {{ number_format($item->price, 2) }} جنيه</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="shipping-details">
        <h2>تفاصيل الشحن</h2>
        <p><strong>الاسم:</strong> {{ $user->name }}</p>
        <p><strong>البريد الإلكتروني:</strong> {{ $user->email }}</p>
        <p><strong>رقم الهاتف:</strong> {{ $user->phone }}</p>
    </div>

    <div class="footer">
        <p>شكراً لاختيارك Mr. Mobiles</p>
        <p>لأي استفسارات، يرجى التواصل معنا على: support@mrmobiles.com</p>
    </div>
</body>

</html>
