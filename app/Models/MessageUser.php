<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageUser extends Base
{
  use HasFactory;

  protected $table = 'message_user';

	protected $fillable = [
		'message_id',
		'user_id',
    'message_state_id',
    'processed'
  ];

  /**
   * Relations
   */

	public function message()
	{
		return $this->belongsTo(Message::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

  public function state()
  {
    return $this->hasOne(MessageState::class, 'id', 'message_state_id');
  }


}
