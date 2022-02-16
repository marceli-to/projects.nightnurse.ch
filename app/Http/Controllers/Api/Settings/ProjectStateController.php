<?php
namespace App\Http\Controllers\Api\Settings;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\ProjectState;
use Illuminate\Http\Request;

class ProjectStateController extends Controller
{
  /**
   * Get a list of projectStates
   * 
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    return new DataCollection(ProjectState::get());
  }

  /**
   * Get a single projectState
   * 
   * @param ProjectState $projectState
   * @return \Illuminate\Http\Response
   */
  public function find(ProjectState $projectState)
  {
    return response()->json(ProjectState::findOrFail($projectState->id));
  }
}
