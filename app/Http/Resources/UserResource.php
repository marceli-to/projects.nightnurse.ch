<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
 
class UserResource extends JsonResource
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
      'company_id' => $this->company_id,
      'team_id' => $this->team_id
    ];
  }
}