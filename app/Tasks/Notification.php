<?php
namespace App\Tasks;

class Notification
{
  public function __invoke()
  {
    $messageUsers = \App\Models\MessageUser::with('user.language', 'message.project', 'message.files', 'message.sender', 'message.users')->where('processed', '=', 0)->get();
    $messageUsers = collect($messageUsers)->splice(0, \Config::get('client.cron_chunk_size'));

    foreach($messageUsers->all() as $m)
    {
      try {
        \Mail::to($m->user->email)->send(new \App\Mail\Notification($m->message));
        $m->processed = 1;
        $m->save();
      } 
      catch(\Throwable $e) {
        $m->processed = 1;
        $m->save();
        \Log::error($e);
      }
    }
  }
}