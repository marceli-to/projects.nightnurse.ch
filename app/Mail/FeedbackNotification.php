<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Feedback;

class FeedbackNotification extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   *
   * @param Message $message
   * @return void
   */
  public function __construct(Feedback $feedback)
  {
    $this->feedback = $feedback;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $subject = 'Feedback von ' . $this->feedback->author . ' (' . $this->feedback->project->number . '/' . $this->feedback->project->name . ')'; 
    return $this->from(env('MAIL_FROM_ADDRESS'), 'Nightnurse Images - Project Room')
                ->replyTo($this->feedback->user->email)
                ->subject($subject)
                ->with(['feedback' => $this->feedback])
                ->markdown('notifications.feedback');
  }
}
