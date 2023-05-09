<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class Notification extends Base
{
	protected $fillable = [
    'title',
    'text',
    'publish',
	];

  /**
   * Scope for published notifications
   */
  
  public function scopePublished($query)
  {
    return $query->where('publish', 1);
  }
}
