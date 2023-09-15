<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Feedback extends Base
{
  use HasEagerLimit;

  protected $casts = [
    'created_at' => 'datetime:d.m.Y H:i',
    'deleted_at' => 'datetime:d.m.Y H:i',
  ];

	protected $fillable = [
    'uuid',
    'rating',
    'comment',
    'project_id',
    'user_id',
	];

  protected $appends = [
    'author',
    'date_string'
  ];

  public function project()
  {
    return $this->hasOne(Project::class, 'id', 'project_id');
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
    return $this->user->firstname . ' ' . $this->user->name . ', ' .$this->user->company->name;
  }

}
