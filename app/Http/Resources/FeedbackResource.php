<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    return [
      'uuid' => $this->uuid,
      'date' => $this->date_string,
      'rating' => $this->rating,
      'comment' => $this->comment,
      'user' => $this->author,
    ];
  }
}
