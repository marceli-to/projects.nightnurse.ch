<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageNotification extends Mailable
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
      ->replyTo($this->message->sender->email)
      ->subject($this->subject)
      ->with(['message' => $this->message])
      ->markdown('notifications.message');

    // TODO: remove this once we have a proper reply-to address
    // make sure we don't use the generic reply-to address in production (yet :-))
    // if (env('APP_ENV') == 'production')
    // {
    //   return $this->from(env('MAIL_FROM_ADDRESS'), 'Nightnurse Images - Project Room')
    //         ->replyTo($this->message->sender->email)
    //         ->subject($this->subject)
    //         ->with(['message' => $this->message])
    //         ->markdown('notifications.message');
    // }

    // return $this->from(env('MAIL_FROM_ADDRESS'), 'Nightnurse Images - Project Room')
    //       ->replyTo('nni+' . $this->message->uuid . '@marceli.to')
    //       ->subject($this->subject)
    //       ->with(['message' => $this->message])
    //       ->markdown('notifications.message');
  }
}
