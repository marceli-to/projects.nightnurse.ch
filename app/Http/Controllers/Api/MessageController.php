<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Message;
use App\Models\MessageFile;
use App\Models\Project;
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
    $message = Message::create([
      'uuid' => \Str::uuid(),
      'subject' => $request->input('subject'),
      'body' => $request->input('body'),
      'private' => $request->input('private'),
      'project_id' => $project->id,
      'user_id' => auth()->user()->id,
    ]);

    if ($request->input('files'))
    {
      foreach($request->input('files') as $file)
      {
        // Move uploaded files
        $media = (new Media())->copy($file['file']);

        // Create database entry
        $messageFile = MessageFile::create([
          'uuid' => \Str::uuid(),
          'name' => $file['file'],
          'original_name' => $file['name'],
          'extension' => $file['extension'] ,
          'size' => $file['size'],
          'preview' => $file['preview'],
          'message_id' => $message->id,
        ]);
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
