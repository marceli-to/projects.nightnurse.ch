<?php
namespace App\Http\Resources;
use App\Models\ReactionType;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageListResource extends JsonResource
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
      'message_date' => $this->message_date,
      'sender' => UserResource::make($this->sender),
    ];
  }
}
