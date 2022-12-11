<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Http\Resources\DataCollection;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Message;
use App\Models\User;
use App\Models\Company;

use App\Http\Resources\MessageResource;
use App\Http\Resources\ProjectResource;

class TestController extends Controller
{
  public function getMessages(Project $project)
  {
    $messages = Message::limitByRole()
      ->with(
        'project.company', 
        'sender', 
        'files', 
        'users', 
        'reactions.user', 
        'reactions.type'
      )
      ->withTrashed()
      ->orderBy('created_at', 'DESC')
      ->where('project_id', $project->id)
      ->get();

    $data = MessageResource::collection($messages)
      ->groupBy('message_date_string')
      ->all();
    
    return response()->json($data);

  }

  public function getProject(Project $project)
  {
    Project::with('state', 'company.users', 'companies.users', 'manager', 'users.company')->findOrFail($project->id);
    $data = ProjectResource::make($project);
    return response()->json($data);
  }

  public function getProjects()
  {
    $ids = ProjectUser::where('user_id', auth()->user()->id)->get()->pluck('project_id');

    $projects = Project::with('company', 'messages.sender')
    ->whereIn('id', $ids)
    ->orderBy('last_activity', 'DESC')
    ->orderBy('number', 'DESC')
    ->get();

    $data = ProjectResource::collection($projects);
    return response()->json($projects);
  }
}
