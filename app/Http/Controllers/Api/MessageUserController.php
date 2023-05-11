<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageUser;
use Illuminate\Http\Request;

class MessageUserController extends Controller
{
  /**
   * Get all users from a message
   * 
   * @param Message $message
   * @return \Illuminate\Http\Response
   */
  public function get(Message $message)
  {
    $users = MessageUser::where('message_id', $message->id)->with('user')->get();
    dd($users);
    return response()->json($users);
  }

}
