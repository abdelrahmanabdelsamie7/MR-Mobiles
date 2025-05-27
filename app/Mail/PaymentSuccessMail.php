<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content,Envelope};
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;
    public $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تم استلام طلبك بنجاح - Mr. Mobiles',
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-success',
            with: [
                'order' => $this->order,
                'user' => $this->order->user,
                'items' => $this->order->orderItems,
                'total' => $this->order->total_price,
                'date' => $this->order->created_at->format('Y-m-d H:i:s'),
            ],
        );
    }
    public function attachments(): array
    {
        return [];
    }
}