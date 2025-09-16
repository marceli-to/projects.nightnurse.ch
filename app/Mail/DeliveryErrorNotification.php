<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\UnstructuredHeader;

class DeliveryErrorNotification extends Mailable
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
      ->subject($this->subject)
      ->with(['message' => $this->message])
      ->withSymfonyMessage(function (Email $email) {
        $vars = json_encode(['message_uuid' => $this->message->uuid], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $email->getHeaders()->add(new UnstructuredHeader('X-Mailgun-Variables', $vars));
      })
      ->markdown('notifications.delivery-error');
  }
}
