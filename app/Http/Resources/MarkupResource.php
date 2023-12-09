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
    $shape = json_decode($this->shape);
    $shape->editable = true;
    $shape->draggable = true;
    $shape->commentable = $this->can_edit && !$this->comment ? true : false;
    
    if ($this->is_locked || !$this->can_edit)
    {
      $shape->editable = false;
      $shape->draggable = false;
      $shape->commentable = false;
    }
    
    return [
      'uuid' => $this->uuid,
      'date' => $this->date_string,
      'shape' => $shape,
      'is_locked' => $this->is_locked,
      'comment' => $this->comment,
      'author' => $this->author,
      'author_company' => $this->author_company,
    ];
  }
}
