<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\DataCollection;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
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
  public function get()
  {
    $users = User::with(['company' => function ($q) { $q->orderBy('name'); }])->get();
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
    return response()->json(User::findOrFail($user->id));
  }

  /**
   * Store a newly created user
   *
   * @param  \Illuminate\Http\UserStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(UserStoreRequest $request)
  {
    $data = $request->all();
    $data['uuid'] = \Str::uuid();
    $data['email_verified_at'] = \Carbon\Carbon::now();
    $user = User::create($data);
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
  public function findAuthenticated()
  {
    $user = User::findOrFail(auth()->user()->id);
    return response()->json(['firstname' => $user->firstname, 'name' => $user->name]);
  }

  /**
   * Change a users email address
   * 
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function updateEmail(UserChangeEmailRequest $request)
  {
    $user = User::findOrFail(auth()->user()->id);
    $user->email = $request->email;
    $user->email_verified_at = null;
    $user->sendEmailVerificationNotification();
    $user->save();
    return response()->json('successfully updated');
  }

}
