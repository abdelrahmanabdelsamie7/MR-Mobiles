<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;
    public $amount;
    public $cartId;
    public function __construct($amount, $cartId)
    {
        $this->amount = $amount;
        $this->cartId = $cartId;
    }
    public function build()
    {
        return $this->subject('Payment Successful')
            ->view('emails.payment_success')
            ->with([
                'amount' => $this->amount,
                'cartId' => $this->cartId,
            ]);
    }
}
