<?php
namespace App\Tasks;

class Feedback
{
  public function __invoke()
  {
    $feedbackUsers = \App\Models\FeedbackQueue::with('user', 'feedback.user')->where('processed', '=', 0)->get();
    $feedbackUsers = collect($feedbackUsers)->splice(0, \Config::get('client.cron_chunk_size'));
    $env = app()->environment();
    
    foreach($feedbackUsers->all() as $m)
    {
      $recipient = ($env == 'production' && $m->user->email) ? $m->user->email : env('MAIL_TO');
      try {
        \Mail::to($recipient)->send(new \App\Mail\Feedback($m->feedback));
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