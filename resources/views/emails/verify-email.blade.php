@component('mail::message')
    # 👋 Hello, {{ $user->name }}!

    Thank you for registering with Mr-Mobiles 🎉
    Please verify your email by clicking the button below.
    🔹 This link is valid for only 30 minutes.
    @component('mail::button', ['url' => $verificationUrl, 'color' => 'success'])
        ✅ Verify Email
    @endcomponent

    If you did not create this account, please ignore this email.

    💡 Need help? Contact our support team anytime.

    Thanks,
    📱 The Mr-Mobiles Team
@endcomponent
