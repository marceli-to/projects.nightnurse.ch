<?php
namespace App\Console\Commands;
use App\Models\Project;
use App\Models\Message;
use App\Services\Media;
use Illuminate\Console\Command;

class MessageUser extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'message:user';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send a message';

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
   
    $messageUsers = \App\Models\MessageUser::with('user.language', 'message.project.manager', 'message.files', 'message.sender', 'message.users')->where('processed', '=', 0)->get();
    $messageUsers = collect($messageUsers)->splice(0, \Config::get('client.cron_chunk_size'));
    $env = app()->environment();
    
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
