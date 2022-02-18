<?php
namespace App\Tasks;

class Notification
{
  public function __invoke()
  {
    $messageUsers = \App\Models\MessageUser::with('user', 'message.project', 'message.files', 'message.sender')->where('processed', '=', 0)->get();
    $messageUsers = collect($messageUsers)->splice(0, \Config::get('client.cron_chunk_size'));

    foreach($messageUsers->all() as $m)
    {
      \Mail::to($m->user->email)->send(new \App\Mail\Notification($m->message));
      $s->processed = 1;
      $s->save();
    }
  }
}