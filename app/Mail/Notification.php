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
    //$this->subject = $this->message->subject ? $this->message->subject . ' – ' . $this->message->project->title : 'Neue Nachricht – ' .  $this->message->project->title;
    $this->subject = $this->message->project->title;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->from(env('MAIL_FROM_ADDRESS'), 'Nightnurse Images - Project Room')
                ->subject($this->subject)
                ->with(['message' => $this->message])
                ->markdown('notifications.message');
  }
}
