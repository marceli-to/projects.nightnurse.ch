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
    'deleted_at' => 'datetime:d.m.Y H:i',
  ];

	protected $fillable = [
    'uuid',
    'subject',
    'body',
    'private',
    'internal',
    'project_id',
    'user_id',
	];

  protected $appends = [
    'message_time',
    'message_date',
    'message_date_string',
    'body_preview',
    'can_delete',
    'truncate_files'
  ];

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
   * Scope for public messages
   * 
   */
  public function scopePublic($query)
  {
    return $query->where('private', 0);
  }

  /**
   * Check if the message belongs to the authorized user
   * and allow them to delete the message
   *
   * @param  string  $value
   * @return boolean
   */
  public function getCanDeleteAttribute($value)
  {
    return $this->user_id == auth()->user()->id ? true : false;
  }

  /**
   * Check if the message belongs to the authorized user
   * and allow them to delete the message
   *
   * @param  string  $value
   * @return boolean
   */
  public function getTruncateFilesAttribute($value)
  {
    return $this->files->count() > 3 ? TRUE : FALSE;
  }


  /**
   * Get the body attribute of a message
   *
   * @param  string  $value
   * @return string
   */
  public function getBodyAttribute($value)
  {
    return str_replace('<p>&nbsp;</p>', '', $value);
  }

  /**
   * Get the time attribute of a message (hours, minutes)
   *
   * @param  string  $value
   * @return string
   */
  public function getMessageTimeAttribute($value)
  {
    return date('H:i', strtotime($this->created_at));
  }

  /**
   * Get the date attribute of a message (day, month, year)
   *
   * @param  string  $value
   * @return string
   */
  public function getMessageDateAttribute($value)
  {
    return date('d.m.Y', strtotime($this->created_at));
  }

  /**
   * Get the date attribute of a message (day, month, year)
   *
   * @param  string  $value
   * @return string
   */
  public function getMessageDateStringAttribute($value)
  {
    $date = \Carbon\Carbon::parse($this->created_at);
    
    if ($date->isToday())
    {
      return 'Today';
    }

    if ($date->isYesterday())
    {
      return 'Yesterday';
    }
    return date('D, d.m.Y', strtotime($this->created_at));
  }

  /**
   * Get a preview of the body field
   *
   * @param  string  $value
   * @return string
   */
  public function getBodyPreviewAttribute($value)
  {
    return \Str::words(strip_tags(str_replace('&nbsp;', '', html_entity_decode(trim($this->body)))), 4, ' ...');
  }

}
