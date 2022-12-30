<?php
namespace App\Services;
use App\Models\Project;
use App\Services\Media;

class ProjectService
{ 

  protected $media;

  public function __construct(Media $media)
  {
    $this->media = $media;
  }
  /**
   * Delete a project entirely
   *
   * @param  Project $project
   * @param  Media $media
   */

  public function delete(Project $project)
  {
    // Get and delete all messages
    $messages = $project->messages;

    // Loop through each message
    foreach ($messages as $message)
    {
      // Get all files for the message
      $files = $message->files;

      // Loop through each file and delete it
      foreach ($files as $file)
      {
        $this->media->remove($file->name);
        $file->delete();
      }

      // Delete the message
      $message->delete();
    }

    // Delete the project
    $project->delete();

    return TRUE;
  }
}
