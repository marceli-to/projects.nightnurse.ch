<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    // 
    \Cookie::forget('nightnurse_images_project_room_session');
    $this->middleware('guest')->except('logout');
  }

  /**
   * Redirect the user based on his role
   * 
   * @return string
   */
  public function redirectTo()
  {
    return RouteServiceProvider::DASHBOARD;
  }
}
