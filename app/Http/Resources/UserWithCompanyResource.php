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
      'id' => auth()->user()->isAdmin() ? $this->id : null,
      'uuid' => $this->uuid,
      'name' => $this->name,
      'firstname' => $this->firstname,
      'email' => $this->email,
      'phone' => $this->phone,
      'short_name' => $this->short_name,
      'full_name' => $this->full_name,
      'register_complete' => $this->register_complete,
      'is_sbb' => $this->is_sbb,
      'company' => [
        'uuid' => $this->company->uuid,
        'name' => $this->company->name,
        'city' => $this->company->city,
        'owner' => $this->company->owner,
      ]
    ];
  }
}