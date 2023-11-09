<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Http\Resources\DataCollection;
use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserInviteRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
  /**
   * Get a list of users
   * 
   * @return \Illuminate\Http\Response
   */

  public function get()
  {
    $users = User::with('company.teams')->orderBy('name')->where('company_id', auth()->user()->company_id)->get();
    return new DataCollection($users);
  }

  /**
   * Get a single user for a given user
   * 
   * @param  $user
   * @return \Illuminate\Http\Response
   */
  public function find(User $user)
  {
    return response()->json(User::with('company')->findOrFail($user->id));
  }

  /**
   * Store a newly created user
   *
   * @param  \Illuminate\Http\EmployeeStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(EmployeeStoreRequest $request)
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
      'company_id' => auth()->user()->company->id,
      'gender_id' => $request->input('gender_id'),
      'role_id' => 2,
      'team_id' => NULL,
      'vertec_id' => NULL,
    ]);
    return response()->json(['userId' => $user->id]);
  }

  /**
   * Update a user for a given user
   *
   * @param User $user
   * @param  \Illuminate\Http\EmployeeUpdateRequest $request
   * @return \Illuminate\Http\Response
   */
  public function update(User $user, EmployeeUpdateRequest $request)
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
    
    if ($user)
    {
      $user->deleted_at = NULL;
      $user->save();
      $this->invite($user);
      return response()->json(['user' => $user]);
    }
    
    // Create user
    $user = User::create([
      'uuid' => \Str::uuid(),
      'email' => $request->input('email'),
      'email_verified_at' => \Carbon\Carbon::now(),
      'language_id' => 1,
      'company_id' => auth()->user()->company->id,
      'gender_id' => 1,
      'role_id' => 2,
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

}
