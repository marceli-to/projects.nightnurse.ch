<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Message;
use App\Http\Requests\MessageStoreRequest;
use Illuminate\Http\Request;

class MessageController extends Controller
{
  /**
   * Get a list of messages
   * 
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    return new DataCollection(Message::with('project', 'sender')->orderBy('created_at')->get());
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
  public function store(MessageStoreRequest $request)
  {
    $data = $request->all();
    $data['uuid'] = \Str::uuid();
    $message = Message::create($data);
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
