<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class MarkupResource extends JsonResource
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
      'type' => $this->type,
      'shape' => json_decode($this->shape),
      'is_owner' => $this->user_id === auth()->user()->id ? TRUE : FALSE,
      'is_locked' => $this->is_locked ? TRUE : FALSE,
      'resizable' => !$this->is_locked && $this->can_edit && $this->type !== 'comment' ? TRUE : FALSE,
      'draggable' => !$this->is_locked && $this->can_edit ? TRUE : FALSE,
      'comment' => $this->comment,
      'author' => $this->author,
      'author_company' => $this->author_company,
    ];
  }
}
