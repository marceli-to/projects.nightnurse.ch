<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
 
class ProjectStateResource extends JsonResource
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
      'description' => $this->description,
    ];
  }
}