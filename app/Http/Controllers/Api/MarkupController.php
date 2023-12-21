<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Markup;
use App\Models\MarkupNotificationQueue;
use App\Models\Message;
use App\Models\MessageFile;
use App\Models\Project;
use App\Http\Resources\MarkupResource;
use App\Http\Resources\MarkupCommentResource;
use App\Http\Requests\MarkupStoreRequest;
use Illuminate\Http\Request;

class MarkupController extends Controller
{
  /**
   * Get a list of markups for a given message file
   * 
   * @return \Illuminate\Http\Response
   */
  public function get(MessageFile $messageFile)
  {
    $project = Project::where('id', $messageFile->message->project_id)->first();
    $this->authorize('containsProject', $project);

    // Get all markups for the message file
    $markups = MarkupResource::collection(
      Markup::where('message_file_id', $messageFile->id)->get()
    );

    // If a markup is not locked and does not belong to the current user, remove it
    foreach ($markups as $key => $markup)
    {
      if ($markup->is_locked == false && $markup->user_id !== auth()->user()->id)
      {
        unset($markups[$key]);
      }
    }

    return response()->json([
      'markups' => $markups,
      'has_unlocked_markups' => $this->hasUnlockedMarkups($messageFile->message_id),
    ]);
  }

  /**
   * Creates a new markup
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    $markup = Markup::create([
      'uuid' => \Str::uuid(),
      'type' => $request->input('element')['type'],
      'shape' => json_encode(
        $request->input('element')['shape']
      ),
      'user_id' => auth()->user()->id,
      'message_id' => MessageFile::where('uuid', $request->input('uuid'))->first()->message_id,
      'message_file_id' => MessageFile::where('uuid', $request->input('uuid'))->first()->id
    ]);

    return response()->json([
      'markup' => MarkupResource::make($markup),
    ]);
  }

  /**
   * Updates a markup
   * 
   * @param Markup $markup
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  
  public function update(Markup $markup, Request $request)
  {
    $markup->shape = json_encode($request->input('element')['shape']);
    $markup->comment = isset($request->input('element')['comment']) ? $request->input('element')['comment'] : NULL;
    $markup->save();
    return response()->json([
      'markup' => MarkupResource::make($markup),
    ]);
  }

  /**
   * Save all markups
   * 
   * @param Message $message
   * @param Boolean $notify
   * @return \Illuminate\Http\Response
   */
  public function save(Message $message, $notify = FALSE)
  {
    $markups = Markup::where('message_id', $message->id)
      ->where('user_id', auth()->user()->id)
      ->where('is_locked', 0)
      ->get();

    foreach ($markups as $markup)
    {
      // Treat comments differently
      if ($markup->type == 'comment')
      { 
        // lock the comment if it has a comment or delete the comment if it doesn't
        if ($markup->comment)
        {
          $markup->is_locked = true;
          $markup->save();
        }
        else
        {
          $markup->delete();
        }
      }
      else
      {
        $markup->is_locked = true;
        $markup->save();
      }
    }

    if ($notify)
    {
      // Notify users
      $this->notify($message);
    }

    return response()->json([
      'message' => 'Markups locked',
      'message_id' => $message->id,
    ]);
  }

  /**
   * Delete a markup
   * 
   * @param Markup $markup
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function delete(Markup $markup)
  {
    if ($markup->user_id !== auth()->user()->id)
    {
      return response()->json([
        'message' => 'You are not allowed to delete this markup',
      ], 403);
    }
    $markup->delete();
    return response()->json([
      'message' => 'Markup deleted',
      'has_unlocked_markups' => $this->hasUnlockedMarkups(
        MessageFile::where('id', $markup->message_file_id)->first()->message_id
      ),
    ]);
  }

  /**
   * Notify all users that a markup has been added
   * 
   * @param Message $message
   * @return void
   */
  public function notify(Message $message)
  {
    // get the project with users by the message
    $project = Project::where('id', $message->project_id)->with('users')->first();

    // if the user is an admin, filter out all users that have role_id = 1
    $users = [];
    if (auth()->user()->isAdmin())
    {
      $users = $project->users->filter(function ($user) {
        return $user->role_id !== 1;
      });
    }
    else
    {
      // if the user is NOT an admin, filter out all users that do not have role id = 1
      $users = $project->users->filter(function ($user) {
        return $user->role_id === 1;
      });

      // add the project owner to the list
      $users->push($project->manager);
    }

    // Loop through all users and create a notification queue entry
    foreach ($users as $user)
    {
      MarkupNotificationQueue::create([
        'message_id' => $message->id,
        'user_id' => $user->id,
        'sender_id' => auth()->user()->id,
      ]);
    }
  }

  /**
   * Check if a message file has unlocked markups
   * 
   * @param Integer $messageId
   * @return boolean
   */
  public function hasUnlockedMarkups($messageId)
  {
    $unlockedMarkups = Markup::where('message_id', $messageId)
      ->where('is_locked', 0)
      ->where('user_id', auth()->user()->id)
      ->get();
    return $unlockedMarkups->count() > 0 ? true : false;
  }
}
