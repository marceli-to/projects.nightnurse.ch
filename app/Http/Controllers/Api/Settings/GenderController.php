<?php
namespace App\Http\Controllers\Api\Settings;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Gender;
use Illuminate\Http\Request;

class GenderController extends Controller
{
  /**
   * Get a list of genders
   * 
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    return new DataCollection(Gender::get());
  }

  /**
   * Get a single gender
   * 
   * @param Gender $gender
   * @return \Illuminate\Http\Response
   */
  public function find(Gender $gender)
  {
    return response()->json(Gender::findOrFail($gender->id));
  }
}
