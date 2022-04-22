<?php
namespace App\Tasks;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CleanUpFiles
{
  public function __invoke()
  {
    // Folder: uploads/temp
    $files = \Storage::listContents('public/uploads/temp');
    collect($files)->each(function($file) {
      if($file['timestamp'] < now()->subMinutes(30)->getTimestamp()) {
        \Storage::delete($file['path']);
      }
    });

    // Folder: downloads/zip
    $files = \Storage::listContents('public/downloads/zip');
    collect($files)->each(function($file) {
      if($file['timestamp'] < now()->subMinutes(30)->getTimestamp()) {
        \Storage::delete($file['path']);
      }
    });
  }
}