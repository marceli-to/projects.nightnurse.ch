<?php
namespace App\Services;
use App\Models\Project;
use App\Models\FeedbackQueue;
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
          // Delete markups
          $file->markups()->delete();
          $file->forceDelete();
        }

        // Delete the message users
        $message->users()->detach();

        // Delete the message reactions
        $message->reactions()->delete();

        // Delete the message
        $message->forceDelete();
      }

      // Delete feedbacks and feedback_queue
      $feedbacks = $project->feedbacks()->get();

      // Loop through each feedback and delete the feedback_queue item
      foreach ($feedbacks as $feedback)
      {
        FeedbackQueue::where('feedback_id', $feedback->id)->delete();
      }

      $project->feedbacks()->delete();

      // Delete companies
      $project->companies()->detach();

      // Delete the project users
      $project->users()->detach();

      // force delete the project
      $project->forceDelete();
      return TRUE;
    }

    // Softdelete should do the following:
    // - set state to 4 (deleted)
    // - delete the project
    // - delete all messages and message files
    // - set 'file_deleted_at' on message files table
    // - physically delete the files from the server

    // Get all messages for this project
    $messages = $project->messages()->get();

    // Loop through each message
    foreach ($messages as $message)
    {
      $files = $message->files()->withTrashed()->get();

      $this->media->removeMany($files);
      $this->media->removeFolder($project->uuid);

      // Loop through each file and delete it
      foreach ($files as $file)
      {
        $file->file_deleted_at = now();
        $file->save();
      }
      // Delete the message
      $message->delete();
    }
    // Set state to 4 (deleted)
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
    // Restore the project
    $project->restore();
    $project->project_state_id = 1;
    $project->save();

    // Restore all messages
    $messages = $project->messages()->withTrashed()->get();
    foreach ($messages as $message)
    {
      $message->restore();
    }
    return TRUE;
  }
}
