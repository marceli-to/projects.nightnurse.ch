<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Base
{
  use HasFactory, SoftDeletes;

  protected $casts = [
    'created_at' => 'datetime:d.m.Y H:i',
  ];

	protected $fillable = [
    'uuid',
    'subject',
    'body',
    'private',
    'project_id',
    'user_id',
	];

  protected $appends = ['feed_date'];


  public function sender()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public function project()
  {
    return $this->hasOne(Project::class, 'id', 'project_id');
  }

	public function users()
	{
		return $this->belongsToMany(User::class);
	}

  public function files()
  {
    return $this->hasMany(MessageFile::class, 'message_id', 'id');
  }

  /**
   * Get the user's short name.
   *
   * @param  string  $value
   * @return string
   */
  public function getFeedDateAttribute($value)
  {
    $date = \Carbon\Carbon::parse($this->created_at);
    
    if ($date->isToday())
    {
      return 'Today, ' . date('H:i', strtotime($this->created_at));
    }

    if ($date->isYesterday())
    {
      return 'Yesterday, ' . date('H:i', strtotime($this->created_at));
    }
    return date('D, d.m.Y', strtotime($this->created_at));
  }
}
