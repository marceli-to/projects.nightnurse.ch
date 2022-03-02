<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Project;
use App\Models\CompanyProject;
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
      return new DataCollection(Project::with('state', 'company', 'companies', 'manager', 'messages')->orderBy('number', 'DESC')->get());
    }
    
    // 1st: try with main company
    $data = Project::byCompanyUser(auth()->user()->company_id)->with('state', 'company', 'companies', 'manager', 'messages')->orderBy('number', 'DESC')->get();
    if ($data->count() > 0)
    {
      return new DataCollection($data);
    }
    else
    {
      // 2nd: try with associated companies
      $data = [];
      $projects = Project::with('state', 'company', 'companies', 'manager', 'messages')->orderBy('number', 'DESC')->get();
      foreach($projects as $project)
      {
        foreach($project->companies as $company)
        {
          if ($company->id == auth()->user()->company_id)
          {
            $data[] = $project;
          }
        }
      }
    }

    return new DataCollection($data);
  }

  /**
   * Get a single projects for a given projects
   * 
   * @param Project $project
   * @return \Illuminate\Http\Response
   */
  public function find(Project $project)
  {
    return response()->json(Project::with('state', 'company.users', 'companies.users', 'manager')->findOrFail($project->id));
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
