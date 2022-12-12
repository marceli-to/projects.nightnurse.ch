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
use App\Http\Resources\UserResource;
use App\Http\Resources\UserWithCompanyResource;
use App\Http\Resources\CompanyWithUsersResource;
use App\Http\Resources\CompanyResource;

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

  public function getProjectUsers(Project $project)
  {
    // Get associated users
    $project = Project::with('users.company')->findOrFail($project->id);

    $project_associates = [];
    $project_associates = $project->users->filter(function ($user) {
      return $user->company->id == Company::OWNER;
    });

    $project_clients = [];
    $project_clients = $project->users->filter(function ($user) {
      return $user->company->id != Company::OWNER;
    });
    
    return $project_clients->toJson(JSON_PRETTY_PRINT);
    return $project->users->toJson(JSON_PRETTY_PRINT);



    // foreach($project->users as $user)
    // {
    //   if ($user->company->id == Company::OWNER)
    //   {
    //     $project_associates[] = UserResource::make($user);
    //   }
    //   else
    //   {
    //     $project_clients[$user->company->id]['data'] = CompanyResource::make($user->company);
    //     $project_clients[$user->company->id]['users'][] = UserResource::make($user);
    //   }
    // }

    return response()->json(['clients' => $project_clients]);



    // Get users for owner
    $owner = Company::owner()->with('teams.users')->first();

    return response()->json(['clients' => $project_clients, 'associates' => $project_associates]);


    return response()->json(['owner' => $owner, 'clients' => $project_clients, 'associates' => $project_associates]);
  }
}
