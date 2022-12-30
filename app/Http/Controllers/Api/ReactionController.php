<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Reaction;
use App\Models\ReactionType;
use App\Models\Message;
use App\Http\Requests\ReactionStoreRequest;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
  /**
   * Store a reaction
   *
   * @param  ReactionStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(ReactionStoreRequest $request)
  {
    $message = Message::where('uuid', $request->input('message_uuid'))->first();
    $reactionType = ReactionType::where('uuid', $request->input('type_uuid'))->first();

    $reaction = Reaction::where('message_id', $message->id)
        ->where('user_id', auth()->user()->id)
        ->where('reaction_type_id', $reactionType->id)
        ->first();

    if ($reaction)
    {
      $reaction->delete();
      return response()->json(true);
    }

    $data = [
      'uuid' => \Str::uuid(),
      'message_id' => $message->id,
      'user_id' => auth()->user()->id,
      'reaction_type_id' => $reactionType->id,
    ];

    $reaction = Reaction::create($data);
    
    return response()->json($reaction);
  }
}