<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\MessageFile;
use App\Models\MessageUser;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\CompanyProject;
use App\Models\User;
use App\Http\Requests\MessageStoreRequest;
use App\Services\Media;
use App\Events\MessageSent;
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
    $this->authorize('containsProject', $project);

    $messages = Message::limitByRole()
      ->with(
        'sender', 
        'files', 
        'users', 
        'message',
        'reactions.user', 
        'reactions.type'
      )
      ->withTrashed()
      ->orderBy('created_at', 'DESC')
      ->where('project_id', $project->id)
      ->get();

    $data = MessageResource::collection($messages)
      ->groupBy('message_date_string')
      ->all();
    
    return response()->json($data);
  }

  /**
   * Get a single messages for a given messages
   * 
   * @param Message $message
   * @return \Illuminate\Http\Response
   */
  public function find(Message $message)
  {
    $message = Message::findOrFail($message->id);
    return response()->json(MessageResource::make($message));
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

    // Reply?
    if ($request->input('message_uuid'))
    {
      $m = Message::where('uuid', $request->input('message_uuid'))->first();
      if ($m)
      {
        $message->message_id = $m->id;
        $message->save();
      }
    }

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
    if ($request->input('users'))
    {
      foreach($request->input('users') as $user)
      {
        $recipient = User::where('uuid', $user['uuid'])->get()->first();
        if ($recipient)
        {
          MessageUser::create([
            'message_id' => $message->id,
            'user_id' => $recipient->id,
            'message_state_id' => 1
          ]);          
        }
      }
    }

    if (!$request->input('private'))
    {
      $project->last_activity = \Carbon\Carbon::now();
      $project->save();
    }

    $user = User::find(auth()->user()->id);
    $message = Message::with('project.company', 'sender', 'files', 'users')->find($message->id);
    $message->can_delete = false;
    broadcast(new MessageSent($user, MessageResource::make($message), $project))->toOthers();
    return response()->json(['messageId' => $message->id]);
  }

  /**
   * Remove a message
   *
   * @param  Message $message
   * @param  Media $media
   * @return \Illuminate\Http\Response
   */
  public function destroy(Message $message, Media $media)
  {
    $message = Message::with('files')->findOrFail($message->id);
    $media->removeMany($message->files);
    $message->delete();
    return response()->json('successfully deleted');
  }
}
