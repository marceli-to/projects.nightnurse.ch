<?php
namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageUser;
use App\Models\User;
use App\Models\Project;
use App\Http\Resources\MessageResource;
use App\Http\Requests\Api\MessageStoreRequest;
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
    $messages = Message::with(
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
   * Store a newly created message
   *
   * @param  \Illuminate\Http\MessageStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(MessageStoreRequest $request)
  { 
    $project = Project::findOrFail($request->input('project_id'));

    // Create message
    $message = Message::create([
      'uuid' => \Str::uuid(),
      'subject' => $request->input('subject'),
      'body' => $request->input('body'),
      'private' => $request->input('private'),
      'internal' => 1,
      'project_id' => $request->input('project_id'),
      'user_id' => $request->input('sender_id'),
    ]);

    // Add users to message user table
    // @todo: verfiy that the user exists in the `project_user` table
    if ($request->input('users'))
    {
      foreach($request->input('users') as $user)
      {
        $user = User::findOrFail($user);
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

}
