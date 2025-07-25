<?php
namespace App\Http\Resources;
use App\Models\MessageFile;
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
      'image_orientation' => $this->image_orientation,
      'image_ratio' => $this->image_ratio,
      'folder' => $this->folder,
      'extension' => $this->extension,
      'preview' => $this->preview,
      'has_preview' => $this->has_preview,
      'size' => \ReadableSize($this->size),
      'locked_markups' => MarkupResource::collection($this->markups->where('is_locked', true)),
      'markups' => MarkupResource::collection($this->markups),
      'markup_count' => $this->markups->count(),
      'message_uuid' => $this->message ? $this->message->uuid : null,
      'file_deleted_at' => $this->file_deleted_at ? date('d.m.Y', strtotime($this->file_deleted_at)) : null
    ];
  }
}