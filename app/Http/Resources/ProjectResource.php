<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Team;

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
      'slug' => $this->slug,
      'title' => $this->title,
      'name' => $this->name,
      'color' => $this->color,
      'date_start' => $this->date_start,
      'date_end' => $this->date_end,
      'workflow' => $this->workflow_uri,
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
      'internal_users' => auth()->user()->isAdmin() ? UserWithCompanyResource::collection(Team::find(Team::TEAM_BUENOS_AIRES)->users) : null,
      'users' => UserWithCompanyResource::collection($this->users),
      'quotes' => ProjectQuoteResource::collection($this->quotes),
    ];
  }
}