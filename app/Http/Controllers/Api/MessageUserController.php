<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageUser;
use App\Http\Resources\UserResource;
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
    $message_users = MessageUser::where('message_id', $message->id)
                      ->where('user_id', '!=', auth()->user()->id)
                      ->with('user')
                      ->get();
    if ($message_users)
    {
      foreach($message_users as $message_user)
      {
        $users[] = UserResource::make($message_user->user);
      }
    }
    return response()->json($users);
  }

}
