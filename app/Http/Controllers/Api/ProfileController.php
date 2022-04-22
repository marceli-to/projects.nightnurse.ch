<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

  /**
   * Get the profile of the authenticated user
   * 
   * @param  $user
   * @return \Illuminate\Http\Response
   */
  public function find()
  {
    return response()->json(User::findOrFail(auth()->user()->id));
  }

  /**
   * Update a user for a given user
   *
   * @param User $user
   * @param  \Illuminate\Http\ProfileUpdateRequest $request
   * @return \Illuminate\Http\Response
   */
  public function update(ProfileUpdateRequest $request)
  {
    $user = User::findOrFail(auth()->user()->id);
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


}
