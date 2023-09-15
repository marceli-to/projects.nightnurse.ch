<?php
namespace App\Tasks;

class Feedback
{
  public function __invoke()
  {
    $feedbackUsers = \App\Models\FeedbackQueue::with('feedback.project', 'feedback.user.company', 'user')->where('processed', '=', 0)->get();
    $feedbackUsers = collect($feedbackUsers)->splice(0, \Config::get('client.cron_chunk_size'));
    $env = app()->environment();
    
    foreach($feedbackUsers->all() as $fu)
    {
      $recipient = ($env == 'production' && $fu->user->email) ? $fu->user->email : env('MAIL_TO');
      try {
        \Mail::to($recipient)->send(new \App\Mail\FeedbackNotification($fu->feedback));
        $fu->processed = 1;
        $fu->save();
      } 
      catch(\Throwable $e) {
        $fu->processed = 1;
        $fu->save();
        \Log::error($e);
      }
    }
  }
}