<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فشلت عملية الدفع | Mr Mobiles</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f7f7f7;
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <i class="fas fa-times-circle text-red-500" style="font-size: 4rem;"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-4">فشلت عملية الدفع</h1>

            @if (isset($error_message))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ $error_message }}
                </div>
            @endif

            <p class="text-gray-600 mb-8">رقم الطلب: <span class="font-semibold">{{ $order_id ?? 'N/A' }}</span></p>

            <div class="space-y-4">
                <a href="/cart"
                    class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition duration-300">
                    العودة إلى السلة
                </a>
                <a href="/"
                    class="inline-block bg-gray-200 text-gray-800 px-8 py-3 rounded-lg hover:bg-gray-300 transition duration-300">
                    العودة للرئيسية
                </a>
            </div>
        </div>
    </div>
</body>

</html>
