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
    $element = json_decode($this->shape);

    if ($element)
    {
      $element->shape->editable = true;
      $element->shape->draggable = true;
      $element->shape->commentable = $this->can_edit && !$this->comment ? true : false;
      
      if ($this->is_locked || !$this->can_edit)
      {
        $element->shape->editable = false;
        $element->shape->draggable = false;
        $element->shape->commentable = false;
      }
    }
   
    return [
      'uuid' => $this->uuid,
      'date' => $this->date_string,
      'shape' => $element ? $element->shape : null,
      'shape_uuid' => $this->shape_uuid,
      'is_locked' => $this->is_locked,
      'comment' => $this->comment,
      'author' => $this->author,
      'author_company' => $this->author_company,
    ];
  }
}
