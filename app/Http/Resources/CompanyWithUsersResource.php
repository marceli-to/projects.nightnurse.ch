<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
 
class CompanyWithUsersResource extends JsonResource
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
      'id' => $this->id,
      'uuid' => $this->uuid,
      'name' => $this->name,
      'full_name' => $this->full_name,
      'city' => $this->city,
      'owner' => $this->owner,
      'users' => UserResource::collection($this->users)
    ];
  }
}