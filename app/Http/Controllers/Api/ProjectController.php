<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Project;
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
    
    // Get users company id
    $companyId = auth()->user()->company_id;

    // Get project with that company (main company)
    $projects = Project::byCompanyUser($companyId)->get()->pluck('id');

    // Get projects with that compnay (associated company)
    $companyProjects = CompanyProject::where('company_id', $companyId)->get()->pluck('project_id');

    // Merge array of ids and remove duplicates
    $ids = array_unique(array_merge($projects->all(), $companyProjects->all()), SORT_REGULAR);

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
   * Get a single projects for a given projects
   * 
   * @param Project $project
   * @return \Illuminate\Http\Response
   */
  public function find(Project $project)
  {
    return response()->json(Project::with('state', 'company.users', 'companies.users', 'manager', 'users')->findOrFail($project->id));
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
    $this->handleCompanies($project, $request->companies);
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
    $this->handleCompanies($project, $request->companies);

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
   * @param Array $buildings
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
}
