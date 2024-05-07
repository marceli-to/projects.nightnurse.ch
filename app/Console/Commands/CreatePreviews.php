<?php
namespace App\Console\Commands;
use App\Models\MessageFile;
use Illuminate\Console\Command;
use Image;

class CreatePreviews extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'create:previews';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Creates previews for all message files';


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

    $files = MessageFile::noPreview()->limit(5)->get();

    foreach ($files as $file) {
      $extension = strtolower($file->extension);
      if (in_array($extension, $previewable_image_types))
      {
        try {
          $response = \Http::get(env('APP_URL') . '/img/small/' . $file->folder . '/' . $file->name);
          $response = \Http::get(env('APP_URL') . '/img/thumbnail/' . $file->folder . '/' . $file->name);
          $file->has_preview = 1;
          $file->save();
        }
        catch (\Exception $e) {
          $this->error('Could not create preview for ' . $file->name);
        }
      }
    }
  }
}
