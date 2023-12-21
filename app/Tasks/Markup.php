<?php
namespace App\Tasks;

class Markup
{
  public function __invoke()
  {
    $markupUsers = \App\Models\MarkupNotificationQueue::with('message.project', 'message.files', 'message.markupMessage', 'message.markupFiles', 'user')->where('processed', '=', 0)->get();
    $markupUsers = collect($markupUsers)->splice(0, \Config::get('client.cron_chunk_size'));
    $env = app()->environment();
    
    foreach($markupUsers->all() as $mu)
    {
      $recipient = ($env == 'production' && $mu->user->email) ? $mu->user->email : env('MAIL_TO');
      try {
        \Mail::to($recipient)->send(new \App\Mail\MarkupMessageNotification($mu->message));
        $mu->processed = 1;
        $mu->save();
      } 
      catch(\Throwable $e) {
        $mu->processed = 1;
        $mu->save();
        \Log::error($e);
      }
    }
  }
}