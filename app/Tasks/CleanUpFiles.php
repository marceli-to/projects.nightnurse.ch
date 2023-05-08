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
      if (isset($file['lastModified']) && $file['lastModified'] < now()->subMinutes(30)->getTimestamp()) {
        \Storage::delete($file['path']);
      }
    });

    // Folder: downloads/zip
    $files = \Storage::listContents('public/downloads/zip');
    collect($files)->each(function($file) {
      if (isset($file['lastModified']) && $file['lastModified'] < now()->subMinutes(30)->getTimestamp()) {
        \Storage::delete($file['path']);
      }
    });

    // Get all subfolders of public/quotes
    $folders = \Storage::directories('public/quotes');
    collect($folders)->each(function($folder) {
      // Get all files in subfolder
      $files = \Storage::listContents($folder);
      collect($files)->each(function($file) {
        // Delete files and folders older than 30 days
        if (isset($file['lastModified']) && $file['lastModified'] < now()->subDays(30)->getTimestamp()) {
          \Storage::delete($file['path']);
          if (count(\Storage::listContents($file['dirname'])) == 0) {
            \Storage::deleteDirectory($file['dirname']);
          }
        }
      });
    });
  }
}