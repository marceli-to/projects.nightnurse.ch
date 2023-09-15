<?php
namespace App\Console\Commands;
use App\Models\Feedback;
use App\Models\Message;
use App\Services\Media;
use Illuminate\Console\Command;

class FeedbackUser extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'feedback:user';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send a feedback';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $feedbackUsers = \App\Models\FeedbackQueue::with('feedback.project', 'feedback.user.company', 'user')->where('processed', '=', 0)->get();
    $feedbackUsers = collect($feedbackUsers)->splice(0, \Config::get('client.cron_chunk_size'));
    $env = app()->environment();
    
    foreach($feedbackUsers->all() as $fu)
    {
      $recipient = ($env == 'production' && $fu->user->email) ? $fu->user->email : env('MAIL_TO');
      try {
        \Mail::to($recipient)->send(new \App\Mail\FeedbackNotification($fu->feedback));
        //$fu->processed = 1;
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
