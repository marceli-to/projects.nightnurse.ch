<?php
namespace App\Http\Resources;
use App\Models\ReactionType;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray($request)
  {
    return [
      'uuid' => $this->uuid,
      'subject' => $this->subject,
      'body' => $this->body,
      'body_preview' => $this->body_preview,
      'internal' => $this->internal,
      'private' => $this->private,
      'can_delete' => $this->can_delete,
      'truncate_files' => $this->files->count() > 3 ? true : false,
      'message_time' => $this->message_time,
      'message_date' => $this->message_date,
      'message_date_string' => $this->message_date_string,
      'deleted_at' => $this->deleted_at,
      'files' => FileResource::collection($this->files),
      'sender' => UserResource::make($this->sender),
      'users' => UserResource::collection($this->users),

      'is_reply' => $this->message ? true : false,
      'original_message' => [
        'uuid' => $this->message ? $this->message->uuid : null,
        'subject' => $this->message ? $this->message->subject : null,
        'body' => $this->message ? $this->message->body : null,
        'body_preview' => $this->message ? $this->message->body_preview : null,
      ],

      // Group reactions by its type and get the users in an array
      'reactions' => $this->reactions->groupBy('reaction_type_id')->map(function ($reactionGroup) {

        // Get the reaction type
        $type = ReactionType::find($reactionGroup->first()->reaction_type_id);

        // Get all users with that reaction
        $users = User::whereIn('id', $reactionGroup->pluck('user_id')->all())->get();
        
        return [
          'type' => [
            'uuid' => $type->uuid,
            'name' => $type->name,
          ],
          'users' => UserResource::collection($users)
        ];
      })
    ];
  }
}
