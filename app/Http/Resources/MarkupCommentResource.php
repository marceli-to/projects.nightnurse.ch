<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class MarkupCommentResource extends JsonResource
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
      'comment' => $this->comment,
      'author' => $this->author,
      'author_company' => $this->author_company,
    ];
  }
}
