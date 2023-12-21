<?php
namespace App\Console\Commands;
use App\Models\Project;
use App\Models\Message;
use App\Services\Media;
use Illuminate\Console\Command;

class MarkupUser extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'markup:user';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send a markup message';

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
    $markupUsers = \App\Models\MarkupNotificationQueue::with('message.project', 'message.files', 'message.markupMessage', 'user', 'sender')->where('processed', '=', 0)->get();
    $markupUsers = collect($markupUsers)->splice(0, \Config::get('client.cron_chunk_size'));
    $env = app()->environment();
    
    foreach($markupUsers->all() as $mu)
    {
      $recipient = ($env == 'production' && $mu->user->email) ? $mu->user->email : env('MAIL_TO');
      try {
        \Mail::to($recipient)->send(new \App\Mail\MarkupMessageNotification($mu->message, $markupUsers[0]->sender));
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
