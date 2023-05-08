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
    // Folder: uploads/temp
    // $files = \Storage::listContents('public/uploads/temp');
    dd(now()->subMinutes(30));
    // collect($files)->each(function($file) {
    //   if (isset($file['timestamp']) && $file['timestamp'] < now()->subMinutes(30)->getTimestamp()) {
    //     \Storage::delete($file['path']);
    //   }
    // });
  }
}
