@component('mail::message')
    # 🔒 Reset Your Password

    Hello, **{{ $user->name }}** 👋

    We received a request to reset your password. Click the button below to set a new one:

    @component('mail::button', [
        'url' => url('http://localhost:4200/reset-password?token=' . $token),
        'color' => 'danger',
    ])
        🔑 Reset Password
    @endcomponent

    🔹 **This link will expire in 1 hour for your security.**

    If you **did not** request this reset, you can safely ignore this email. **Your password will remain unchanged.**

    📞 Need help? Contact our support team.

    Thanks,
    **🛠️ The Mr-Mobiles Team**
@endcomponent
