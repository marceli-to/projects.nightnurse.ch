<?php
namespace App\Services;
use App\Models\Project as ProjectModel;
use App\Models\Message;
use App\Services\Media;

class Project
{ 
  /**
   * Delete a project entirely
   *
   * @param  ProjectModel $project
   */

  public function delete(ProjectModel $project)
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
        $media = (new Media())->remove($file->name);
        $file->delete();
      }

      // Delete the message
      $message->delete();
    }

    // Delete the project
    $project->delete();
  }
}
