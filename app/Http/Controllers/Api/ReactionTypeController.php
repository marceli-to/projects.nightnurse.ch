<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ReactionType;
use App\Http\Resources\ReactionTypeResource;
use Illuminate\Http\Request;

class ReactionTypeController extends Controller
{
  /**
   * Get a list of reactions
   * 
   * @return \Illuminate\Http\Response
   */

  public function get()
  {
    return response()->json(
      ReactionTypeResource::collection(
        ReactionType::get()
      )
    );
  }

}