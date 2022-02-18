<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class Notification extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   *
   * @param Message $message
   * @return void
   */
  public function __construct(Message $message)
  {
    $this->message = $message;
    $this->subject = $this->message->subject ? $this->message->subject : 'Neue Nachricht von ' . $this->message->sender->short_name;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->from(\Config::get('client.email.from'), 'Emergency Room – Nightnurse')
                ->subject($this->subject)
                ->with(['message' => $this->message])
                ->markdown('notifications.message');
  }
}
