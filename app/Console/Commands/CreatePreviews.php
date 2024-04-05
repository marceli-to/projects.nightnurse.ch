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

    $files = MessageFile::noPreview()->limit(2)->get();



    // for each file, create a preview with the filters
    foreach ($files as $file) {
      if (in_array($file->extension, $previewable_image_types)) {

        $this->info('Processing ' . $file->name);
        $response = \Http::get(env('APP_URL') . '/img/small/' . $file->folder . '/' . $file->name);
        $response = \Http::get(env('APP_URL') . '/img/thumbnail/' . $file->folder . '/' . $file->name);
        $this->info('Processed ' . $file->name);

        $file->has_preview = 1;
        $file->save();
        $this->info('Updated record ' . $file->name);

      }
    }

  }
}
