<?php
namespace App\Console\Commands;
use App\Models\Project;
use App\Models\Message;
use Illuminate\Console\Command;

class Cleanup extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'cleanup:files';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Clean up old message files';

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
    $this->info('Cleaning files');
    $projects = Project::archive()->with('messages.files')->get();
    $this->info('Found ' . $projects->count() . ' projects');
  }
}
