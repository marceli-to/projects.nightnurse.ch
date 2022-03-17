<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UserRegisterUpdateRequest;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
  protected $viewPath = 'auth.';

  /**
   * Show the register form
   *
   * @param User $user
   * @return \Illuminate\Http\Response
   */

  public function index(User $user)
  {
    return view($this->viewPath . 'register', ['uuid' => $user->uuid]);
  }

  /**
   * Update a user for a given user
   *
   * @param User $user
   * @param  \Illuminate\Http\UserRegisterUpdateRequest $request
   * @return \Illuminate\Http\Response
   */
  public function update(UserRegisterUpdateRequest $request)
  {
    $user = User::where('uuid', $request->input('uuid'))->firstOrFail();
    $user->update($request->except(['password', 'passwort_confirmation']));
    $user->password = \Hash::make($request->input('password'));
    $user->register_complete = 1;
    $user->save();
    return redirect(route('complete'));
  }

  /**
   * Show the complete message
   *
   * @return \Illuminate\Http\Response
   */

  public function complete()
  {
    return view($this->viewPath . 'register-complete');
  }


}
