<?php
namespace App\Policies;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
  use HandlesAuthorization;

  /**
   * Determine whether the user can access a project
   *
   * @param  \App\Models\User $user
   * @param  \App\Models\Project $project
   * @return \Illuminate\Auth\Access\Response|bool
   */
  public function containsProject(User $user, Project $project)
  {
    $user_projects = $user->projects()->withTrashed()->get();
    return $user_projects->contains($project->id) || auth()->user()->isAdmin();
  }

}
