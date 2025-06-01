<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $verificationUrl;
    public function __construct($user)
    {
        $this->user = $user;
        $this->verificationUrl = url('/api/user/verify-email/' . $this->user->verification_token);
    }
    public function build()
    {
        $name  = $this->user->first_name . ' ' . $this->user->last_name;
        return $this->subject('Verify Your Email')
            ->view('emails.verify-email')
            ->with([
                'userName' => $name ,
                'verificationUrl' => $this->verificationUrl,
            ]);
    }
}