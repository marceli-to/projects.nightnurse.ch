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
   * @param  Boolean $forceDelete
   */

  public function delete(Project $project, $forceDelete = FALSE)
  {
    if ($forceDelete)
    {
      // Get all messages (including soft deleted) for this project
      $messages = $project->messages()->withTrashed()->get();

      // Loop through each message
      foreach ($messages as $message)
      {
        // Get all files for the message
        $files = $message->files;

        $this->media->removeMany($files);
        $this->media->removeFolder($project->uuid);

        // Loop through each file and delete it
        foreach ($files as $file)
        {
          $file->delete();
        }

        // Delete the message users
        $message->users()->detach();

        // Delete the message
        $message->forceDelete();
      }

      // Delete companies
      $project->companies()->detach();

      // Delete the project users
      $project->users()->detach();

      // force delete the project
      $project->forceDelete();
      return TRUE;
    }

    // Delete the project
    $project->delete();
    return TRUE;
  }
}
