<?php
namespace App\Console\Commands;
use App\Models\MessageFile;
use Illuminate\Console\Command;

class AddRatio extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'add:ratio';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Adds ratio to all message files';


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
    $previewable_image_types = [
      'png', 'jpg', 'jpeg', 'gif'
    ];
    $files = MessageFile::withTrashed()->get();
    $bar = $this->output->createProgressBar(count($files));
    foreach ($files as $file)
    {
      // if $file->extension is not in $previewable_image_types, skip
      if (!in_array(strtolower($file->extension), $previewable_image_types))
      {
        $bar->advance();
        $this->info('Skipping ' . $file->name);
        continue;
      }

      $bar->advance();
      // file location is either /storage/uploads/{file->folder}/{file->name} (if folder is not null)
      // or /storage/uploads/{file->name} (if folder is null)
      $file_location = storage_path('app/public/uploads/' . ($file->folder ? $file->folder . '/' : '') . $file->name);
      // if the file does not exist, skip
      if (!file_exists($file_location))
      {
        continue;
      }
      // get the image size
      $size = getimagesize($file_location);
      // if the image size is not empty
      if ($size)
      {
        // set the ratio
        $file->image_ratio = $size[0]. '/'. $size[1];
        // save the file
        $file->save();
      }
    }

  }
}
