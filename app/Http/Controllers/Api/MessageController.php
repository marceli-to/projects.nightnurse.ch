<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Message;
use App\Models\MessageFile;
use App\Models\MessageUser;
use App\Models\Project;
use App\Models\User;
use App\Http\Requests\MessageStoreRequest;
use App\Services\Media;
use Illuminate\Http\Request;

class MessageController extends Controller
{
  /**
   * Get a list of messages
   * 
   * @param Project $project
   * @return \Illuminate\Http\Response
   */
  public function get(Project $project)
  {
    return new DataCollection(Message::with('project', 'sender', 'files')->orderBy('created_at', 'DESC')->where('project_id', $project->id)->get());
  }

  /**
   * Get a single messages for a given messages
   * 
   * @param Message $message
   * @return \Illuminate\Http\Response
   */
  public function find(Message $message)
  {
    return response()->json(Message::findOrFail($message->id));
  }

  /**
   * Store a newly created message
   *
   * @param  \Illuminate\Http\MessageStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(Project $project, MessageStoreRequest $request)
  { 
    // Create message
    $message = Message::create([
      'uuid' => \Str::uuid(),
      'subject' => $request->input('subject'),
      'body' => $request->input('body'),
      'private' => $request->input('private'),
      'internal' => auth()->user()->company->owner ? 1 : 0,
      'project_id' => $project->id,
      'user_id' => auth()->user()->id,
    ]);

    // Move file & create database entry
    if ($request->input('files'))
    {
      foreach($request->input('files') as $file)
      {
        $media = (new Media())->copy($file['name']);

        $messageFile = MessageFile::create([
          'uuid' => \Str::uuid(),
          'name' => $file['name'],
          'original_name' => $file['original_name'],
          'extension' => $file['extension'] ,
          'size' => $file['size'],
          'preview' => $file['preview'],
          'message_id' => $message->id,
        ]);
      }
    }

    // Add users to message user table
    // @todo: check if a user has "no notifications enabled" and set "processed" to 1
    if ($request->input('users'))
    {
      foreach($request->input('users') as $user)
      {
        $user = User::where('uuid', $user)->get()->first();
        if ($user)
        {
          MessageUser::create([
            'message_id' => $message->id,
            'user_id' => $user->id,
            'message_state_id' => 1
          ]);          
        }
      }
    }
    
    return response()->json(['messageId' => $message->id]);
  }

  /**
   * Remove a message
   *
   * @param  Message $message
   * @return \Illuminate\Http\Response
   */
  public function destroy(Message $message)
  {
    $message->delete();
    return response()->json('successfully deleted');
  }
}
