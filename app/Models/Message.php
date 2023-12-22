<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Message extends Base
{
  use HasFactory, SoftDeletes, HasEagerLimit;

  protected $casts = [
    'created_at' => 'datetime:d.m.Y H:i',
    'deleted_at' => 'datetime:d.m.Y H:i',
  ];

	protected $fillable = [
    'uuid',
    'subject',
    'body',
    'intermediate',
    'private',
    'internal',
    'project_id',
    'user_id',
    'message_id',
	];

  protected $appends = [
    'message_time',
    'message_date_time',
    'message_date',
    'message_date_string',
    'body_preview',
    'can_delete',
    'truncate_files',
    'private_internal',
  ];

  public function sender()
  {
    return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
  }

  public function project()
  {
    return $this->hasOne(Project::class, 'id', 'project_id')->withTrashed();
  }

  public function message()
  {
    return $this->hasOne(Message::class, 'id', 'message_id');
  }

  public function markupMessage()
  {
    return $this->hasOne(Message::class, 'id', 'markup_message_id');
  }

	public function users()
	{
		return $this->belongsToMany(User::class)->withTrashed();
	}

  public function files()
  {
    return $this->hasMany(MessageFile::class, 'message_id', 'id');
  }

  public function markupFiles()
  {
    return $this->hasMany(MessageFile::class, 'message_id', 'markup_message_id');
  }

  public function reactions()
  {
    return $this->hasMany(Reaction::class);
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
   * Scope for public messages
   * 
   */
  public function scopePrivate($query)
  {
    return $query->where('private', 1);
  }

  /**
   * Scope for messages flagged as intermediate (Zwischenstand)
   * 
   */

  public function scopeIntermediate($query)
  {
    return $query->where('intermediate', 1)->where('private', 0);
  }

  /**
   * Scope for public messages
   * 
   */
  public function scopeLimitByRole($query)
  {
    if (auth()->user()->isClient())
    {
      return $query->where('private', 0);
    }
    return $query;
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
    return $this->user_id == auth()->user()->id && !$this->markupFiles ? true : false;
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
    //return $this->files->count() > 3 ? TRUE : FALSE;
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
   * Get the time attribute of a message (hours, minutes)
   *
   * @param  string  $value
   * @return string
   */
  public function getMessageDateTimeAttribute($value)
  {
    $date = \Carbon\Carbon::parse($this->created_at);

    // if date is today add the time only
    if ($date->isToday())
    {
      return date('H:i', strtotime($this->created_at));
    }

    return date('d.m.Y', strtotime($this->created_at));
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
    return \Str::words(strip_tags(str_replace(array('&nbsp;', '<br>', '<br />'), array('', ' ', ' '), html_entity_decode(trim($this->body)))), 4, ' ...');
  }

  /**
   * Get the team ba attribute of a message
   *
   * @param  string  $value
   * @return string
   */
  public function getPrivateInternalAttribute($value)
  {
    return $this->sender->team_id == 1 && $this->private && $this->internal ? true : false;
  }

}
