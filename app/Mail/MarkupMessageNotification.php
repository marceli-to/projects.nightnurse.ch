<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\User;

class MarkupMessageNotification extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   *
   * @param Message $message
   * @return void
   */
  public function __construct(Message $message, User $sender)
  {
    $this->message = $message;
    $this->sender = $sender;
    $this->subject = $this->message->subject ? $this->message->project->title . ' - ' . $this->message->subject : $this->message->project->title;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->from(env('MAIL_FROM_ADDRESS'), 'Nightnurse Images - Project Room')
      ->subject('Neue Markierungen/Kommentare')
      ->with(['message' => $this->message, 'sender' => $this->sender])
      ->markdown('notifications.markup');
  }
}
