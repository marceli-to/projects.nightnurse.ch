<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class Invitation extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   *
   * @param User $user
   * @return void
   */
  public function __construct(User $user)
  {
    $this->user = $user;
    $this->subject = 'Einladung Project Room – Nightnurse';
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->from(\Config::get('client.email.from'), 'Nightnurse Images - Project Room')
                ->subject($this->subject)
                ->with(['user' => $this->user])
                ->markdown('notifications.invitation');
  }
}
