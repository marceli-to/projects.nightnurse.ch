<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Project;
use App\Models\CompanyProject;
use Illuminate\Http\Request;

class CompanyProjectController extends Controller
{
  /**
   * Get a list of company project items
   * 
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    return new DataCollection(CompanyProject::get());
  }

  /**
   * Get a list of company project items for a project
   * 
   * @param Project $project
   * @return \Illuminate\Http\Response
   */
  public function findByProject(Project $project)
  {
    return new DataCollection(CompanyProject::with('company')->where('project_id', $project->id)->get());
  }

  /**
   * Remove a companyProject
   *
   * @param  CompanyProject $companyProject
   * @return \Illuminate\Http\Response
   */
  public function destroy(CompanyProject $companyProject)
  {
    $companyProject->delete();
    return response()->json('successfully deleted');
  }
}
