<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Company;
use App\Models\CompanyProject;
use App\Services\VertecApi;
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
      // Get 'users projects'
      $user_projects = Project::with('state', 'company', 'companies', 'manager', 'messages.sender')
                    ->orderBy('last_activity', 'DESC')
                    ->orderBy('number', 'DESC')
                    ->where('user_id', auth()->user()->id)
                    ->get();

      // Get 'all projects'
      $projects = Project::with('state', 'company', 'companies', 'manager', 'messages.sender')
                    ->orderBy('last_activity', 'DESC')
                    ->orderBy('number', 'DESC')
                    ->where('user_id', '!=', auth()->user()->id)
                    ->get();

      return response()->json(['user_projects' => $user_projects, 'projects' => $projects]);
    }
    
    // -- Access by company (v1)
    // // Get users company id
    $companyId = auth()->user()->company_id;

    // // Get project with that company (main company)
    $projects = Project::byCompanyUser($companyId)->get()->pluck('id');

    // // Get projects with that company (associated company)
    $companyProjects = CompanyProject::where('company_id', $companyId)->get()->pluck('project_id');

    // // Merge array of ids and remove duplicates
    $ids = array_unique(array_merge($projects->all(), $companyProjects->all()), SORT_REGULAR);
    // -- Access by company (v1)


    // -- Access by user (v2)
    // Get user projects
    // $ids = ProjectUser::where('user_id', auth()->user()->id)->get()->pluck('project_id');
    // -- Access by user (v2)
    
    // Map fields
    $projects = Project::with('company', 'messages.sender')
      ->whereIn('id', $ids)
      ->orderBy('last_activity', 'DESC')
      ->orderBy('number', 'DESC')
      ->get()
      ->map(function($p) {
        return [
          'uuid' => $p->uuid,
          'number' => $p->number,
          'color' => $p->color,
          'name' => $p->name,
          'company' => [
              'uuid' => $p->company->uuid,
              'name' => $p->company->name,
              'city' => $p->company->city,
          ],
          'messages' => $p->messages->map(function($m) {
            return [
              'uuid' => $m->uuid,
              'subject' => $m->subject,
              'body' => $m->body,
              'body_preview' => $m->body_preview,
              'message_date' => $m->message_date,
              'sender' => [
                'full_name' => $m->sender ? $m->sender->full_name : '',
              ]
            ];
          }),
        ];
    });
    return response()->json(['projects' => collect($projects)]);
  }

  /**
   * Get a list of users associated with the project
   * 
   * @param Project $project
   * @return \Illuminate\Http\Response
   */

  public function getUsers(Project $project)
  {
    $project = Project::with('users.company')->findOrFail($project->id);

    // Get users from owner company (NNI)
    $users = $project->users->filter(function($value, $key) {
      return $value->company->owner == 1;
    });

    $owner = [
      'data' => Company::owner()->get()->first(),
      'users' => $users->all()
    ];

    // Get users from clients
    $users = $project->users->filter(function($value, $key) {
      return $value->company->owner == 0;
    });

    foreach($users as $user)
    {
      $clients[$user->company->id]['data'] = $user->company;
      $clients[$user->company->id]['users'][] = $user;
    }

    return response()->json(['owner' => $owner, 'clients' => $clients]);
  }

  /**
   * Get a single projects for a given projects
   * 
   * @param Project $project
   * @return \Illuminate\Http\Response
   */
  public function find(Project $project)
  {
    return response()->json(Project::with('state', 'company.users', 'companies.users', 'manager', 'users.company')->findOrFail($project->id));
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
    $this->handleCompanies($project, $request->companies);
    $this->handleUsers($project, $request->users);

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
    $project->save();

    // Handle companies & users (pivot tables)
    $this->handleCompanies($project, $request->companies);
    $this->handleUsers($project, $request->users);

    // Make changes in 'Vertec'
    $vertec = new VertecApi();
    $vertec->updateProject($project);

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
   * @param  Project $project
   * @return \Illuminate\Http\Response
   */
  public function destroy(Project $project)
  {
    $project->delete();
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
