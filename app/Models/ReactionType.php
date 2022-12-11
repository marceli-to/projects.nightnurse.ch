<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class ReactionType extends Base
{

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */

  protected $fillable = [
    'uuid',
    'name',
  ];

  /**
   * The courses that belong to this category
   */
  
  public function reactions()
  {
    return $this->hasMany(Reaction::class);
  }

}
