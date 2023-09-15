<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FeedbackQueue extends Model
{
  protected $table = 'feedback_queue';

  protected $fillable = [
    'feedback_id',
    'user_id',
    'processed',
  ];

  /**
   * Relations
   */

  public function feedback()
  {
    return $this->belongsTo(Feedback::class);
  }

  public function user()
  {
  return $this->belongsTo(User::class);
  }
}
