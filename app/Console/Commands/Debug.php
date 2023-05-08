<?php
namespace App\Console\Commands;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;

class Debug extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'debug';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Debug errors';

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
    // // Folder: uploads/temp
    // $files = \Storage::listContents('public/uploads/temp');
    // collect($files)->each(function($file) {
    //   if ($file->lastModified() < now()->subMinutes(30)->getTimestamp()) {
    //     \Storage::delete($file['path']);
    //   }
    //   else {
    //     echo $file->lastModified();
    //   }
    // });
    // Get all subfolders of public/quotes
    $folders = \Storage::directories('public/quotes');
    collect($folders)->each(function($folder) {
      // Get all files in subfolder
      $files = \Storage::listContents($folder);
      collect($files)->each(function($file) {
        // Delete files and folders older than 30 days
        if ($file->lastModified() < now()->subDays(30)->getTimestamp()) {
          \Storage::delete($file['path']);
          dd($file->path());
          dd(\Storage::listContents($file['path']));
          if (count(\Storage::listContents($file['path'])) == 0) {
            \Storage::deleteDirectory($file['path']);
          }
        }
      });
    });
  }
}
