<?php
namespace App\Notifications;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CustomVerifyEmail extends VerifyEmail
{
    use Queueable;
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }

        $token = Str::random(64);
        $notifiable->verification_token = $token;
        $notifiable->save();

        Log::info('Generated verification token', [
            'user_id' => $notifiable->id,
            'token' => $token
        ]);

        return (new MailMessage)
            ->subject('Verify Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', url('/api/user/verify-email/' . $token))
            ->line('If you did not create an account, no further action is required.');
    }
}