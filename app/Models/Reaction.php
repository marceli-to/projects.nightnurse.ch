<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Base
{

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */

  protected $fillable = [
    'uuid',
    'user_id',
    'message_id',
    'reaction_type_id',
  ];

  /**
   * The courses that belong to this category
   */
  
  public function message()
  {
    return $this->belongsTo(Message::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function type()
  {
    return $this->belongsTo(ReactionType::class, 'reaction_type_id', 'id');
  }
}
