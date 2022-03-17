<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Http\Resources\DataCollection;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class UserController extends Controller
{
  /**
   * Get a list of users
   * 
   * @return \Illuminate\Http\Response
   */

  public function get(Company $company)
  {
    $users = User::with('company')->orderBy('name')->where('company_id', $company->id)->get();
    return new DataCollection($users);
  }

  /**
   * Get a list of users
   * 
   * @return \Illuminate\Http\Response
   */
  public function getStaff()
  {
    return new DataCollection(User::staff()->get());
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
      'gender_id' => $request->input('gender_id'),
      'role_id' => $request->input('role_id'),
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
    $user->update($request->all());
    $user->save();
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
   * Get users info by authenticated user
   */
  public function getAuthenticated()
  {
    $user = User::findOrFail(auth()->user()->id);

    $data = [
      'firstname' => $user->firstname, 
      'name' => $user->name,
      'email' => $user->email,
    ];

    if ($user->isAdmin())
    {
      $data['admin'] = TRUE;
    }

    return response()->json($data);
  }

  /**
   * Quick register for external users
   *
   * @param  \Illuminate\Http\UserRegisterRequest $request
   * @return \Illuminate\Http\Response
   */

  public function register(UserRegisterRequest $request)
  {
    // get company
    $company = Company::where('uuid', $request->input('company_uuid'))->get()->first();

    // create user
    $user = User::create([
      'uuid' => \Str::uuid(),
      'email' => $request->input('email'),
      'email_verified_at' => \Carbon\Carbon::now(),
      'language_id' => 1,
      'company_id' => $company->id,
      'gender_id' => 1,
      'role_id' => 2,
    ]);

    if ($user)
    {
      try {
        \Mail::to($user->email)->send(new \App\Mail\Invitation($user));
      } 
      catch(\Throwable $e) {
        \Log::error($e);
      }     
    }

    return response()->json(['userId' => $user->id]);
  }

}
