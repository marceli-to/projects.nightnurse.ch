<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\DataCollection;
use App\Http\Resources\MessageResource;
use App\Http\Resources\MarkupCommentResource;
use App\Models\Message;
use App\Models\MessageFile;
use App\Models\MessageUser;
use App\Models\Markup;
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
        'files.markups', 
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
      'intermediate' => $request->input('intermediate') ? $request->input('intermediate') : 0,
      'internal' => auth()->user()->company->owner ? 1 : 0,
      'project_id' => $project->id,
      'user_id' => auth()->user()->id,
    ]);

    // Has markup message?
    if ($request->input('markupMessage'))
    {
      $message->markup_message_id = $request->input('markupMessage');
      $message->save();
    }

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
    $faultyFiles = [];
    if ($request->input('files'))
    {
      foreach($request->input('files') as $file)
      {
        $filename = (new Media())->copy($file['name'], $project->uuid);

        // make sure the file is stored in the storage
        if (Storage::exists('public/uploads' . DIRECTORY_SEPARATOR . $project->uuid . DIRECTORY_SEPARATOR . $filename))
        {
          $messageFile = MessageFile::create([
            'uuid' => \Str::uuid(),
            'name' => $filename,
            'original_name' => $file['original_name'],
            'image_orientation' => $file['image_orientation'],
            'image_ratio' => $file['image_ratio'],
            'folder' => $project->uuid,
            'extension' => $file['extension'] ,
            'size' => $file['size'],
            'preview' => $file['preview'],
            'has_preview' => $file['instant_previewable'],
            'message_id' => $message->id,
          ]);
        }
        else {
          \Log::error('File not found', [
            'path' => 'public/uploads' . DIRECTORY_SEPARATOR . $project->uuid . DIRECTORY_SEPARATOR . $filename,
            'full_path' => Storage::path('public/uploads' . DIRECTORY_SEPARATOR . $project->uuid . DIRECTORY_SEPARATOR . $filename),
            'disk' => config('filesystems.default'),
            'permissions' => @fileperms(Storage::path('public/uploads' . DIRECTORY_SEPARATOR . $project->uuid . DIRECTORY_SEPARATOR . $filename)),
          ]);
          $faultyFiles[] = $file['name'];
        }
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

    // Persist the sender's copy preference and optionally add them as a recipient
    $sender = User::find(auth()->user()->id);
    $sendCopy = $request->boolean('send_copy');
    $sender->send_copy = $sendCopy;
    $sender->save();

    if ($sendCopy)
    {
      MessageUser::create([
        'message_id' => $message->id,
        'user_id'    => $sender->id,
        'message_state_id' => 1,
      ]);
    }

    // Update last_activity only for public messages since private messages 
    // are not shown in the project list and therefore don't need to update
    if (!$request->input('private'))
    {
      $project->last_activity = \Carbon\Carbon::now();
      $project->save();
    }

    if (!$request->input('markupMessage'))
    {
      $user = User::find(auth()->user()->id);
      $message = Message::with('project.company', 'sender', 'files', 'users')->find($message->id);
      $message->can_delete = false;
      // wrap broadcast in try catch to prevent error when running tests
      try {
        broadcast(new MessageSent($user, MessageResource::make($message), $project))->toOthers();
      } catch (\Exception $e) {
        // log error
        \Log::error($e->getMessage());
      }
    }

    return response()->json(['messageId' => $message->id, 'faultyFiles' => $faultyFiles]);
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
