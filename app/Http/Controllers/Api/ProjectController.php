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
      $user_projects = Project::active()->with('state', 'company', 'companies', 'manager')
                      ->with(['messages' => function ($query) {
                        $query->with('sender')->limit(3);
                      }])
                      ->orderBy('last_activity', 'DESC')
                      ->orderBy('number', 'DESC')
                      ->where('user_id', auth()->user()->id)
                      ->get();
        

      // Get 'all projects'
     $projects = Project::active()->with('state', 'company', 'companies', 'manager')
                    ->with(['messages' => function ($query) {
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
   * Get a list of archived projects
   * 
   * @return \Illuminate\Http\Response
   */
  public function getArchive()
  {
    if (auth()->user()->isAdmin())
    {
      $user_projects = Project::archive()->with('state', 'company', 'companies', 'manager')
                      ->with(['messages' => function ($query) {
                        $query->with('sender')->limit(3);
                      }])
                      ->orderBy('last_activity', 'DESC')
                      ->orderBy('number', 'DESC')
                      ->where('user_id', auth()->user()->id)
                      ->get();
        

      // Get 'all projects'
     $projects = Project::archive()->with('state', 'company', 'companies', 'manager')
                    ->with(['messages' => function ($query) {
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

    $clients = [];
    foreach($project->users as $user)
    {
      $clients[$user->company->id]['data'] = $user->company;
      $clients[$user->company->id]['users'][] = $user;
    }

    // Get users for owner (nni)
    $owner = Company::owner()->with('users')->get()->first();
    return response()->json(['owner' => $owner, 'clients' => $clients]);
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
    $project->save();

    // Handle companies & users (pivot tables)
    $companies = collect($request->input('companies'))->pluck('id');
    $project->companies()->sync($companies);

    $users = collect($request->input('users'))->pluck('id');
    $project->users()->sync($users);

    // Make changes in 'Vertec'
    if (app()->environment() == 'production')
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
