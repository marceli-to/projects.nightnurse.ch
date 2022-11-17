<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Message;
use App\Models\MessageFile;
use App\Models\MessageUser;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\CompanyProject;
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
    if (auth()->user()->isAdmin())
    {
      $messages = new DataCollection(Message::with('project', 'sender', 'files', 'users')->withTrashed()->orderBy('created_at', 'DESC')->where('project_id', $project->id)->get());
      $messagesGrouped = $messages->groupBy('message_date_string');
      return $messagesGrouped->all();
    }

    // Access check based on projects
    $hasAccess = ProjectUser::where('project_id', $project->id)->where('user_id', auth()->user()->id)->get()->first();
    if (!$hasAccess)
    {
      return abort(403);
    }

    // Map fields
    $messages = Message::public()
      ->with('project', 'sender', 'files')
      ->withTrashed()
      ->orderBy('created_at', 'DESC')
      ->where('project_id', $project->id)
      ->get()
      ->map(function($m) {
      return [
        'uuid' => $m->uuid,
        'subject' => $m->subject,
        'body' => $m->body,
        'internal' => $m->internal,
        'can_delete' => $m->can_delete,
        'truncate_files' => $m->files->count() > 3 ? true : false,
        'message_time' => $m->message_time,
        'message_date' => $m->message_date,
        'message_date_string' => $m->message_date_string,
        'deleted_at' => $m->deleted_at,
        'files' => $m->files->map(function($f) {
          return [
            'uuid' => $f->uuid,
            'name' => $f->name,
            'original_name' => $f->original_name,
            'extension' => $f->extension,
            'preview' => $f->preview,
            'size' => $f->size,
          ];
        }),
        'sender' => [
          'short_name' => $m->sender->short_name,
          'full_name' => $m->sender->full_name,
          'name' => $m->sender->name,
          'firstname' => $m->sender->firstname,
        ],
        'users' => $m->users->map(function($u) {
          return [
            'uuid' => $u->uuid,
            'full_name' => $u->full_name,
            'short_name' => $u->short_name,
            'email' => $u->email,
          ];
        }),
      ];
    });

    $messages = collect($messages);
    $messagesGrouped = $messages->groupBy('message_date_string');
    return response()->json($messagesGrouped->all());
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

    $project->last_activity = \Carbon\Carbon::now();
    $project->save();
    
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
    $message = Message::with('files')->findOrFail($message->id);
    (new Media())->removeMany($message->files);
    $message->delete();
    return response()->json('successfully deleted');
  }
}
