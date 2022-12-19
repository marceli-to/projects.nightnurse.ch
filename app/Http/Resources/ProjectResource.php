<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;


class ProjectResource extends JsonResource
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
      'number' => $this->number,
      'title' => $this->title,
      'name' => $this->name,
      'color' => $this->color,
      'date_start' => $this->date_start,
      'date_end' => $this->date_end,
      'last_activity'=> $this->last_activity,
      'manager' => UserResource::make($this->manager),
      'user_id' => $this->manager->id,
      'project_state_id' => $this->state->id,
      'state' => [
        'id' => $this->state->id,
        'description' => $this->state->description
      ],
      'company_id' => $this->company->id,
      'company' => CompanyWithUsersResource::make($this->company),
      'companies' => CompanyWithUsersResource::collection($this->companies),
      'users' => UserWithCompanyResource::collection($this->users)
    ];
  }
}