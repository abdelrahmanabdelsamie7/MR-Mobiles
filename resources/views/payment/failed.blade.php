<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فشل عملية الدفع | Mr Mobiles</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full text-center">
        <div class="mb-4">
            <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">فشلت عملية الدفع</h1>
        <p class="text-gray-600 mb-2">رقم الطلب: {{ $order_id }}</p>
        @if ($error_message)
            <p class="text-red-600 mb-6">{{ $error_message }}</p>
        @endif
        <div class="space-y-4">
            <a href="/"
                class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition-colors">
                العودة للرئيسية
            </a>
            <a href="/cart" class="block text-blue-600 hover:text-blue-800">
                العودة إلى سلة المشتريات
            </a>
        </div>
    </div>
</body>

</html>
