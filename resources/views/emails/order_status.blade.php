<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>حالة الطلب</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: right;
            direction: rtl;
        }

        .email-container {
            background: #fff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 30px auto;
        }

        h2 {
            color: #333;
            margin-bottom: 15px;
        }

        p {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .status-created {
            color: #007bff;
        }

        .status-confirmed {
            color: #28a745;
            font-weight: bold;
        }

        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            color: #444;
        }

        ul li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        ul li strong {
            width: 130px;
            display: inline-block;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h2>مرحباً {{ $order->user->first_name }} {{ $order->user->last_name }} 👋</h2>

        @if ($status === 'created')
            <p class="status-created">تم استلام طلبك بنجاح. نحن نراجع معلومات الدفع وسنقوم بتحديثك قريباً.</p>
        @elseif ($status === 'confirmed')
            <p class="status-confirmed">🎉 تم تأكيد طلبك! سيتم تجهيز الطلب وإرساله إليك في أقرب وقت ممكن.</p>
        @elseif ($status === 'rejected')
            <p class="status-rejected">❌ للأسف، تم رفض الطلب. يرجى مراجعة معلومات الدفع أو التواصل مع الدعم.</p>
        @endif

        <h4>تفاصيل الطلب:</h4>
        <ul>
            <li><strong>رقم الطلب:</strong> {{ $order->id }}</li>
            <li><strong>طريقة الدفع:</strong> {{ $order->payment_method }}</li>
            <li><strong>إجمالي السعر:</strong> {{ number_format($order->total_price, 2) }} جنيه</li>
            <li><strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</li>
        </ul>

        <p>إذا كان لديك أي استفسار، لا تتردد في التواصل معنا.</p>

        <div class="footer">
            <p>مع تحياتنا،<br>فريق الدعم</p>
        </div>
    </div>
</body>

</html>
