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
   * @return Boolean
   */

  public function delete(Project $project, $forceDelete = FALSE)
  { 
    // Force delete
    if ($forceDelete)
    {
      // Get all messages (including soft deleted) for this project
      $messages = $project->messages()->withTrashed()->get();

      // Loop through each message
      foreach ($messages as $message)
      {
        // Get all files for the message
        $files = $message->files()->withTrashed()->get();

        $this->media->removeMany($files);
        $this->media->removeFolder($project->uuid);

        // Loop through each file and delete it
        foreach ($files as $file)
        {
          $file->forceDelete();
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

    // Softdelete
    $project->project_state_id = 4;
    $project->save();

    // Delete the project
    $project->delete();
    return TRUE;
  }

  /**
   * Restore a project
   * 
   * @param Project $project
   * @return Boolean
   */

  public function restore(Project $project)
  {
    $project->restore();
    $project->project_state_id = 1;
    $project->save();
    return TRUE;
  }
}
