<?php
namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\User;
use App\Models\Project;
use App\Http\Resources\FeedbackResource;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
  /**
   * Get all feedbacks grouped by project
   * 
   * @param Project $project
   * @return \Illuminate\Http\Response
   */

  public function get()
  {    
    $feedbacks = Feedback::with(
      'project', 
      'user', 
    )
    ->orderBy('created_at', 'DESC')
    ->get();

    // Group feedbacks by project name
    $feedbacks = $feedbacks->groupBy(function ($item, $key) {
      return $item['project']['name'];
    });

    return response()->json($feedbacks);
  }

  /** 
   * Get all feedbacks for a project
   * @param Project $project
   * @return \Illuminate\Http\Response
   */
  public function find(Project $project)
  {
    $feedbacks = Feedback::with(
        'project', 
        'user', 
      )
      ->orderBy('created_at', 'DESC')
      ->where('project_id', $project->id)
      ->get();
    
    return response()->json($feedbacks);
  }

}
