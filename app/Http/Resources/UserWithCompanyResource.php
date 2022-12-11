<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
 
class UserWithCompanyResource extends JsonResource
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
      'firstname' => $this->firstname,
      'email' => $this->email,
      'short_name' => $this->short_name,
      'full_name' => $this->full_name,
      'register_complete' => $this->register_complete,
      'company' => [
        'uuid' => $this->uuid,
        'name' => $this->name,
        'city' => $this->city,
        'owner' => $this->owner,
      ]
    ];
  }
}