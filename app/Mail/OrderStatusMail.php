<?php
namespace App\Mail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;
    public $order;
    public $status;
    public function __construct(Order $order, $status)
    {
        $this->order = $order;
        $this->status = $status;
    }
    public function build()
    {
        $subject = match ($this->status) {
            'created' => 'تم استلام طلبك بنجاح',
            'confirmed' => 'تم تأكيد طلبك',
            'rejected' => 'تم رفض طلبك',
            default => 'حالة الطلب'
        };
        return $this->subject($subject)
                    ->view('emails.order_status');
    }
}
