<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
 
class FileResource extends JsonResource
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
      'name' => $this->name,
      'original_name' => $this->original_name,
      'folder' => $this->folder,
      'extension' => $this->extension,
      'preview' => $this->preview,
      'size' => $this->size,
    ];
  }
}