<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم الدفع بنجاح | Mr Mobiles</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f7f7f7;
        }

        .success-icon {
            animation: scale 0.5s ease-in-out;
        }

        @keyframes scale {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="success-icon mb-6">
                <svg class="w-24 h-24 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-4">تم الدفع بنجاح!</h1>
            <p class="text-gray-600 mb-8">شكراً لك على ثقتك بنا. سيتم إرسال تفاصيل الطلب إلى بريدك الإلكتروني.</p>
            <div class="space-y-4">
                <a href="/"
                    class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
                    العودة للرئيسية
                </a>
                <div class="text-sm text-gray-500">
                    رقم الطلب: <span class="font-semibold">{{ $order_id ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
