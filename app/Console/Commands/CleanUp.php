<?php
namespace App\Console\Commands;
use App\Models\Project;
use App\Models\Message;
use App\Services\Media;
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
    
    // Get all deleted projects
    $projects = Project::onlyTrashed()->limit(2)->get();

    $this->info('Found ' . $projects->count() . ' projects');

    // Loop through each project
    foreach ($projects as $project)
    {
      // Get all messages for the project (including soft deleted ones)
      $messages = Message::withTrashed()->where('project_id', $project->id)->get();
  
      // Loop through each message
      foreach ($messages as $message)
      {
        // Get all files for the message
        $files = $message->files;

        // Loop through each file and delete it
        foreach ($files as $file)
        {
          // File to delete
          $this->info('File to delete: ' . $file->name);
          // $media = (new Media())->trash($file->name, $file->folder);
          // // Set file_deleted_at to current time
          // $file->file_deleted_at = now();
          // $this->info('deleted file: ' . $file->name);
        }
      }
    }
  }
}
