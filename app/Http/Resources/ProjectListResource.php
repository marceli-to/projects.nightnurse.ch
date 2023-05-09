<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectListResource extends JsonResource
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
      'number' => $this->number,
      'name' => $this->name,
      'color' => $this->color,
      'last_activity'=> $this->last_activity,
      'company' => CompanyResource::make($this->company),
      'preview_messages' => MessageListResource::collection($this->previewMessages),
    ];
  }
}