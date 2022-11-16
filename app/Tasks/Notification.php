<?php
namespace App\Tasks;

class Notification
{
  public function __invoke()
  {
    $messageUsers = \App\Models\MessageUser::with('user.language', 'message.project', 'message.files', 'message.sender', 'message.users')->where('processed', '=', 0)->get();
    $messageUsers = collect($messageUsers)->splice(0, \Config::get('client.cron_chunk_size'));
    $env  = app()->environment();
    
    foreach($messageUsers->all() as $m)
    {
      $recipient = ($env == 'production' && $m->user->email) ? $m->user->email : env('MAIL_TO');
      try {
        \Mail::to($recipient)->send(new \App\Mail\Notification($m->message));
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