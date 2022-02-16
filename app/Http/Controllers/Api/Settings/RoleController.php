<?php
namespace App\Http\Controllers\Api\Settings;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
  /**
   * Get a list of roles
   * 
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    return new DataCollection(Role::get());
  }

  /**
   * Get a single role
   * 
   * @param Role $role
   * @return \Illuminate\Http\Response
   */
  public function find(Role $role)
  {
    return response()->json(Role::findOrFail($role->id));
  }
}
