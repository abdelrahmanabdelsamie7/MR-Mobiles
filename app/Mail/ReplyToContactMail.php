<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class ReplyToContactMail extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $replyMessage;
    public function __construct($name, $replyMessage)
    {
        $this->name = $name;
        $this->replyMessage = $replyMessage;
    }
    public function build()
    {
        return $this->subject('Reply to Your Inquiry')
            ->view('emails.reply_to_contact');
    }
}
