<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\ProjectListResource;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\ProjectQuote;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyProject;
use App\Models\Team;
use App\Services\VertecApi;
use App\Services\ProjectService;
use App\Http\Requests\ProjectStoreRequest;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
  /**
   * Get a list of projects
   * 
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    if (auth()->user()->isAdmin())
    {
      $user_projects = Project::with('state', 'company', 'companies', 'manager')
                      ->with(['previewMessages' => function ($query) {
                        $query->with('sender')->limit(3);
                      }])
                      ->orderBy('last_activity', 'DESC')
                      ->orderBy('number', 'DESC')
                      ->where('user_id', auth()->user()->id)
                      ->orWhereHas('users', function ($query) {
                        $query->where('user_id', auth()->user()->id);
                      })
                      ->active()
                      ->get();
      
      // Get 'all projects'
      $projects = Project::active()->with('state', 'company', 'companies', 'manager')
                    ->with(['previewMessages' => function ($query) {
                      $query->with('sender')->limit(3);
                    }])
                    ->orderBy('last_activity', 'DESC')
                    ->orderBy('number', 'DESC')
                    ->where('user_id', '!=', auth()->user()->id)
                    ->get();
      return response()->json(['user_projects' => $user_projects, 'projects' => $projects]);
    }
  
    // Get user projects
    $ids = ProjectUser::where('user_id', auth()->user()->id)->get()->pluck('project_id');
    $projects = Project::with('company')
      ->with(['previewMessages' => function ($query) {
        $query->with('sender')->limit(3);
      }])
      ->whereIn('id', $ids)
      ->orderBy('last_activity', 'DESC')
      ->orderBy('number', 'DESC')
      ->get();

    return response()->json(['projects' => ProjectListResource::collection($projects)]);
  }

  /**
   * Get a list of archived projects
   * 
   * @return \Illuminate\Http\Response
   */
  public function getArchive()
  {
    if (auth()->user()->isAdmin())
    {
      $user_projects = Project::archive()->with('state', 'company', 'companies', 'manager')
                      ->with(['previewMessages' => function ($query) {
                        $query->with('sender')->limit(3);
                      }])
                      ->orderBy('last_activity', 'DESC')
                      ->orderBy('number', 'DESC')
                      ->where('user_id', auth()->user()->id)
                      ->get();
        

      // Get 'all projects'
     $projects = Project::archive()->with('state', 'company', 'companies', 'manager')
                    ->with(['previewMessages' => function ($query) {
                      $query->with('sender')->limit(3);
                    }])
                    ->orderBy('last_activity', 'DESC')
                    ->orderBy('number', 'DESC')
                    ->where('user_id', '!=', auth()->user()->id)
                    ->get();

      return response()->json(['user_projects' => $user_projects, 'projects' => $projects]);
    }

  }

  /**
   * Get a list of users associated with the project
   * 
   * @param Project $project
   * @return \Illuminate\Http\Response
   */

  public function getProjectUsers(Project $project)
  {
    // Get associated users
    $project = Project::with('users.company')->findOrFail($project->id);

    $project_clients = [];
    $project_associates = [];

    foreach($project->users as $user)
    {
      if ($user->company->id == Company::OWNER)
      {
        $project_associates[] = UserResource::make($user);
      }
      else
      {
        $project_clients[$user->company->id]['data'] = CompanyResource::make($user->company);
        $project_clients[$user->company->id]['users'][] = UserResource::make($user);
      }
    }

    // Get users for owner
    $owner = Company::owner()->with('teams.users')->first();

    if (auth()->user()->isAdmin())
    {
      return response()->json(
        [
          'owner' => $owner, 
          'clients' => $project_clients, 
          'associates' => $project_associates,
          'isProjectManager' => $project->user_id == auth()->user()->id
        ]
      );
    }

    // Map fields for non-admins
    $project_clients = [];
    $project_associates = [];
    foreach($project->users as $user)
    {
      if ($user->company->id == Company::OWNER)
      {
        $project_associates[] = [
          'uuid' => $user->uuid,
          'firstname' => $user->firstname,
          'name' => $user->name,
          'full_name' => $user->full_name,
          'short_name' => $user->short_name,
          'register_complete' => $user->register_complete,
          'email' => $user->register_complete ? null : $user->email,
          'team_id' => $user->team->id
        ];
      }
      else
      {
        $project_clients[$user->company->uuid]['data'] = [
          'uuid' => $user->company->uuid,
          'name' => $user->company->name,
          'full_name' => $user->company->full_name,
          'city' => $user->company->city,
        ];
        $project_clients[$user->company->uuid]['users'][] = [
          'uuid' => $user->uuid,
          'firstname' => $user->firstname,
          'name' => $user->name,
          'full_name' => $user->full_name,
          'short_name' => $user->short_name,
          'register_complete' => $user->register_complete,
          'email' => $user->register_complete ? null : $user->email
        ];
      }
    }

    $data = [
      'owner' => [
        'uuid' => $owner->uuid,
        'name' => $owner->name,
        'full_name' => $owner->full_name,
        'city' => $owner->city,
        'teams' => $owner->teams->where('id', TEAM::TEAM_ZURICH)->map(function($t) {
          return [
            'uuid' => $t->uuid,
            'description' => $t->description,
            'users' => $t->users->map(function($u) use ($t) {
              return [
                'uuid' => $u->uuid,
                'firstname' => $u->firstname,
                'name' => $u->name,
                'full_name' => $u->full_name,
                'short_name' => $u->short_name,
                'register_complete' => $u->register_complete,
                'email' => $u->register_complete ? null : $u->email,
                'team_id' => $t->id
              ];
            })
          ];
        }),
      ],
      'clients' => $project_clients,
      'associates' => $project_associates,
      'isProjectManager' => false,
    ];
    return response()->json($data);
  }

  /**
   * Get a project with its associated companies
   * 
   * @param Project $project
   * @return \Illuminate\Http\Response
   */

  public function getProjectCompanies(Project $project)
  {
    return response()->json(Project::with('company.users', 'companies.users')->findOrFail($project->id));
  }

  /**
   * Get a single projects for a given projects
   * 
   * @param Project $project
   * @return \Illuminate\Http\Response
   */
  public function find(Project $project)
  {
    $project = Project::with(
      'state', 
      'company.users', 
      'company.teams',
      'companies.users', 
      'manager', 
      'users.company',
      'messages',
      'quotes',
    )->findOrFail($project->id);
    return response()->json(ProjectResource::make($project));
  }

  /**
   * Store a newly created project
   *
   * @param  \Illuminate\Http\ProjectStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(ProjectStoreRequest $request)
  {
    $data = $request->all();
    $data['uuid'] = \Str::uuid();
    $project = Project::create($data);

    // Handle companies & users (pivot tables)
    $companies = collect($request->input('companies'))->pluck('id');
    $project->companies()->sync($companies);

    $users = collect($request->input('users'))->pluck('id');
    $project->users()->sync($users);

    return response()->json(['projectId' => $project->id]);
  }

  /**
   * Update a project for a given project
   *
   * @param Project $project
   * @param  \Illuminate\Http\ProjectStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function update(Project $project, ProjectStoreRequest $request)
  {
    $project = Project::findOrFail($project->id);
    $project->update($request->all());

    // Handle companies & users (pivot tables)
    $companies = collect($request->input('companies'))->pluck('id');
    $project->companies()->sync($companies);

    $users = collect($request->input('users'))->pluck('id');
    $project->users()->sync($users);

    // Make changes in 'Vertec'
    if (app()->environment() == 'production' || app()->environment() == 'staging')
    {
      $vertec = new VertecApi();
      $vertec->updateProject($project);
    }

    return response()->json('successfully updated');
  }

  /**
   * Toggle the status a given project
   *
   * @param  Project $project
   * @return \Illuminate\Http\Response
   */
  public function toggle(Project $project)
  {
    $project->publish = $project->publish == 0 ? 1 : 0;
    $project->save();
    return response()->json($project->publish);
  }

  /**
   * Remove a project
   *
   * @param Project $project
   * @param ProjectService $projectService
   * @return \Illuminate\Http\Response
   */
  public function destroy(Project $project, ProjectService $projectService)
  {
    $projectService->delete($project);
    return response()->json('successfully deleted');
  }

  /**
   * Store or update pivot data
   * 
   * @param Project $project
   * @param Array $companies
   * 
   * @return void
   */
  protected function handleCompanies(Project $project, $companies)
  {
    $project->companies()->detach();
    foreach($companies as $c)
    { 
      $record = new CompanyProject([
        'company_id' => $c['id'],
        'project_id' => $project->id,
      ]);
      $record->save();
    }
  }

  /**
   * Store or update pivot data
   * 
   * @param Project $project
   * @param Array $users
   * 
   * @return void
   */
  protected function handleUsers(Project $project, $users)
  {
    $project->users()->detach();
    foreach($users as $u)
    { 
      $record = new ProjectUser([
        'project_id' => $project->id,
        'user_id' => $u['id'],
      ]);
      $record->save();
    }
  }
}
