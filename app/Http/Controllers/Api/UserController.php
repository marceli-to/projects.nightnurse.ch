<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Http\Resources\DataCollection;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserInviteRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class UserController extends Controller
{
  /**
   * Get a list of users by company
   * 
   * @return \Illuminate\Http\Response
   */

  public function get(Company $company)
  {
    $users = User::with('company.teams')->orderBy('name')->where('company_id', $company->id)->get();
    return new DataCollection($users);
  }

  /**
   * Get a list of all users
   * 
   * @return \Illuminate\Http\Response
   */

  public function all($grouped = FALSE)
  {
    if ($grouped)
    {
      $users = User::with('company.teams')->orderBy('name')->get()->groupBy('company.name');
      // order by company name
      $users = $users->sortBy(function ($user, $key) {
        return $user->first()->company->name;
      });
      return new DataCollection($users);
    }

    $users = User::with('company.teams')->orderBy('name')->get();
    return new DataCollection($users);
  }


  /**
   * Get a list of users
   * 
   * @return \Illuminate\Http\Response
   */
  public function getStaff()
  {
    return new DataCollection(User::staff()->orderBy('firstname')->get());
  }

  /**
   * Get users info by authenticated user
   */
  public function getAuthenticated()
  {
    $user  = User::with('language')->findOrFail(auth()->user()->id);
    $data = [
      'uuid' => $user->uuid,
      'company' => [
        'uuid' => $user->company->uuid,
        'name' => $user->company->name,
      ],
      'firstname' => $user->firstname, 
      'name' => $user->name,
      'email' => $user->email,
      'language' => $user->language->acronym,
    ];

    if ($user->isClientAdmin())
    {
      $data['client_admin'] = TRUE;
      $data['can'] = [
        'edit_project_access' => TRUE,
        'edit_users' => TRUE,
      ];
    }

    if ($user->isAdmin())
    {
      $data['admin'] = TRUE;
      $data['can'] = [
        'create_private_message' => TRUE,
        'create_public_message' => $user->team_id == 1 ? TRUE : FALSE,
        'access_private_messages' => TRUE,
        'access_public_messages' => TRUE,
      ];
    }

    return response()->json($data);
  }

  /**
   * Get a single user for a given user
   * 
   * @param  $user
   * @return \Illuminate\Http\Response
   */
  public function find(User $user)
  {
    return response()->json(User::with('company.teams')->findOrFail($user->id));
  }

  /**
   * Store a newly created user
   *
   * @param  \Illuminate\Http\UserStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(UserStoreRequest $request)
  {
    $user = User::create([
      'uuid' => \Str::uuid(),
      'firstname' => $request->input('firstname'),
      'name' => $request->input('name'),
      'email' => $request->input('email'),
      'phone' => $request->input('phone'),
      'email_verified_at' => \Carbon\Carbon::now(),
      'password' => \Hash::make($request->input('password')),
      'language_id' => $request->input('language_id'),
      'company_id' => $request->input('company_id'),
      'team_id' => $request->input('company_id') ? $request->input('company_id') : NULL,
      'gender_id' => $request->input('gender_id'),
      'role_id' => $request->input('role_id'),
      'vertec_id' => $request->input('vertec_id') ? $request->input('vertec_id') : NULL,
    ]);
    return response()->json(['userId' => $user->id]);
  }

  /**
   * Update a user for a given user
   *
   * @param User $user
   * @param  \Illuminate\Http\UserUpdateRequest $request
   * @return \Illuminate\Http\Response
   */
  public function update(User $user, UserUpdateRequest $request)
  {
    $user = User::findOrFail($user->id);
    $user->update($request->except(['password', 'password_confirmation']));

    if ($request->input('password') && $request->input('password_confirmation'))
    {
      $validated = $request->validate([
        'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
        'password_confirmation' => 'min:8',
      ]);
      $user->password = \Hash::make($request->input('password'));
      $user->save();
    }
    return response()->json('successfully updated');
  }

  /**
   * Toggle the status a given user
   *
   * @param  User $user
   * @return \Illuminate\Http\Response
   */
  public function toggle(User $user)
  {
    $user->publish = $user->publish == 0 ? 1 : 0;
    $user->save();
    return response()->json($user->publish);
  }

  /**
   * Remove a user
   *
   * @param  User $user
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user)
  {
    $user->delete();
    return response()->json('successfully deleted');
  }

  /**
   * Quick register of a user. User will be sent an email to
   * complete the registering process.
   *
   * @param UserInviteRequest $request
   * @return \Illuminate\Http\Response
   */

  public function register(UserInviteRequest $request)
  {
    // Check for existing but deleted user first
    $user = User::withTrashed()->where('email', $request->input('email'))->get()->first();

    if (!$this->validateDomain($request))
    {
      return response()->json(['error' => 'invalid_domain']);
    }
    
    if ($user)
    {
      $user->deleted_at = NULL;
      $user->save();
      $this->invite($user);
      return response()->json(['user' => $user]);
    }
    
    // Get company
    $company = Company::where('uuid', $request->input('company_uuid'))->get()->first();

    // Create user
    $user = User::create([
      'uuid' => \Str::uuid(),
      'email' => $request->input('email'),
      'email_verified_at' => \Carbon\Carbon::now(),
      'language_id' => 1,
      'company_id' => $company->id,
      'gender_id' => 1,
      'role_id' => $company->owner ? 1 : 2,
      'team_id' => $company->owner ? $company->id : NULL,
    ]);
    $this->invite($user);
    return response()->json(['user' => $user]);
  }

  /**
   * Send an invitation e-mail to new users
   *
   * @param  User $user
   * @return \Illuminate\Http\Response
   */

  public function invite(User $user)
  {
    if ($user)
    {
      try {
        \Mail::to($user->email)->send(new \App\Mail\Invitation($user));
      } 
      catch(\Throwable $e) {
        \Log::error($e);
      }     
    }
    return false;
  }

  /**
   * Validate domain
   *
   * @param Request $request
   * @return \Illuminate\Http\Response
   */

  protected function validateDomain(Request $request)
  {
    
    if($request->input('has_domain_confirmation'))
    {
      return true;
    }

    // get company by uuid
    $company = Company::with('users')->where('uuid', $request->input('company_uuid'))->get()->first();

    // get domain from email
    $emailDomain = explode('@', $request->input('email'))[1];

    // check if domain is in company email domains
    $valid = false;

    // get most used email domain from company->users
    $domains = [];
    foreach ($company->users as $user)
    {
      $domains[] = explode('@', $user->email)[1];
    }
    $domain = array_count_values($domains);
    arsort($domain);
    $domain = array_key_first($domain);

    if ($domain == $emailDomain)
    {
      $valid = true;
    }
    return $valid;
  }

}
