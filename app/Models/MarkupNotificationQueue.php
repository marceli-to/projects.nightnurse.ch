<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MarkupNotificationQueue extends Model
{
  protected $table = 'markup_notification_queue';

  protected $fillable = [
    'message_id',
    'user_id',
    'sender_id',
    'processed',
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

  public function sender()
  {
    return $this->belongsTo(User::class);
  }
}
