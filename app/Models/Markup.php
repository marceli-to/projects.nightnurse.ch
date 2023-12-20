<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Markup extends Model
{
  use SoftDeletes;

  protected $casts = [
    'created_at' => 'datetime:d.m.Y H:i',
    'deleted_at' => 'datetime:d.m.Y H:i',
  ];

	protected $fillable = [
    'uuid',
    'type',
    'shape',
    'comment',
    'is_locked',
    'message_id',
    'message_file_id',
    'user_id',
	];

  protected $appends = [
    'author',
    'author_company',
    'date_string',
    'can_edit',
  ];

  // Relations
  public function messageFile()
  {
    return $this->hasOne(MessageFile::class, 'id', 'message_file_id');
  }

  public function message()
  {
    return $this->hasOne(Message::class, 'id', 'message_id');
  }

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}

    /**
   * Get the date attribute of a message (day, month, year)
   *
   * @param  string  $value
   * @return string
   */
  public function getDateStringAttribute($value)
  {
    $date = \Carbon\Carbon::parse($this->created_at);
    return date('d.m.Y', strtotime($this->created_at));
  }

  public function getAuthorAttribute()
  {
    return $this->user->short_name;
  }

  public function getAuthorCompanyAttribute()
  {
    return $this->user->company->name ?? null;
  }

  public function getCanEditAttribute()
  {
    return auth()->user()->id === $this->user_id ? TRUE : FALSE;
  }

}
