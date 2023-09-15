<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Feedback;
use App\Models\FeedbackQueue;
use App\Http\Resources\FeedbackResource;
use App\Models\Project;
use App\Http\Requests\FeedbackStoreRequest;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
  /**
   * Get a list of feedbacks for a given project
   * 
   * @return \Illuminate\Http\Response
   */
  public function get(Project $project)
  {
    $this->authorize('containsProject', $project);
    
    $feedbacks = auth()->user()->isAdmin() ? 
      Feedback::where('project_id', $project->id)->with('user')->get() :
      Feedback::where('project_id', $project->id)->with('user')->where('user_id', auth()->user()->id)->limit(1)->get();

    return response()->json([
      'data' => FeedbackResource::collection($feedbacks),
      'show_form' => !auth()->user()->isAdmin() && $feedbacks->isEmpty() ? TRUE : FALSE,
    ]);
  }

  /**
   * Store a newly created feedback
   *
   * @param  \Illuminate\Http\FeedbackStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(FeedbackStoreRequest $request)
  {
    $project = Project::where('uuid', $request->input('project_uuid'))->firstOrFail();
    
    // Store Feedback
    $feedback = Feedback::create([
      'uuid' => \Str::uuid(),
      'rating' => $request->input('rating'),
      'comment' => $request->input('comment'),
      'project_id' => $project->id,
      'user_id' => auth()->user()->id,
    ]);

    // Get project manager and add to feedback queue
    $manager = $project->manager()->first();
    FeedbackQueue::create([
      'feedback_id' => $feedback->id,
      'user_id' => $manager->id,
    ]);

    // Get project members and add to feedback queue
    $members = $project->users()->where('company_id', 1)->get();
    foreach($members as $member)
    {
      FeedbackQueue::create([
        'feedback_id' => $feedback->id,
        'user_id' => $member->id,
      ]);
    }

    return response()->json(['feedback' => FeedbackResource::make($feedback)]);
  }

}
