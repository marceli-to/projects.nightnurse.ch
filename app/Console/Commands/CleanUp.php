<?php
namespace App\Console\Commands;
use App\Models\Project;
use App\Models\Message;
use App\Models\MessageFile;
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
    // Get all files in /storage/app/public/uploads
    $files = scandir(storage_path('app/public/uploads'));

    // Loop over the files
    foreach ($files as $file)
    {
      // ignore . and .. and .DS_Store and temp
      if ($file == '.' || $file == '..' || $file == '.DS_Store' || $file == 'temp') continue;

      // if its a directory, loop over the files in the directory
      if (is_dir(storage_path('app/public/uploads/' . $file)))
      {
        $subFiles = scandir(storage_path('app/public/uploads/' . $file));
        foreach ($subFiles as $subFile)
        {
          if ($subFile == '.' || $subFile == '..' || $subFile == '.DS_Store' || $subFile == 'temp') continue;
          // get the record from the database with relationship to message and project
          $messageFile = MessageFile::where('name', $subFile)->first();

          if (!$messageFile) continue;

          // get the message
          $message = $messageFile->message;

          if (!$message) continue;

          // get the project
          $project = $message->project;

          if (!$project) continue;

          $project = Project::withTrashed()->find($project->id);

          // check if the project is deleted
          if ($project->deleted_at)
          {
            echo $project->id;
            echo "\n";
          }
        }
      }
      else
      {
        // get the record from the database with relationship to message and project
        $messageFile = MessageFile::where('name', $file)->first();

        if (!$messageFile) continue;

        // get the message
        $message = $messageFile->message;

        if (!$message) continue;

        // get the project
        $project = $message->project;

        if (!$project) continue;

        $project = Project::withTrashed()->find($project->id);

        // check if the project is deleted
        if ($project->deleted_at)
        {
          echo $project->id;
          echo "\n";
        }
      }

    }
    
    // $this->info('Cleaning files');
    
    // // Get all deleted projects
    // $projects = Project::onlyTrashed()->get();

    // $this->info('Found ' . $projects->count() . ' projects');

    // // Loop through each project
    // foreach ($projects as $project)
    // {
    //   // Get all messages for the project (including soft deleted ones)
    //   $messages = Message::withTrashed()->where('project_id', $project->id)->get();
  
    //   // Loop through each message
    //   foreach ($messages as $message)
    //   {
    //     // Get all files for the message
    //     $files = $message->files;

    //     // Loop through each file and delete it
    //     foreach ($files as $file)
    //     {
    //       $media = (new Media())->trash($file->name, $file->folder);
    //       // Set file_deleted_at to current time
    //       $file->file_deleted_at = now();
    //       $file->save();
    //       $this->info('deleted file: ' . $file->name);
    //     }
    //   }
    // }
  }
}
