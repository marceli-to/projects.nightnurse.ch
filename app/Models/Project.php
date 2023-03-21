<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Project extends Base
{
  use HasFactory, SoftDeletes, HasEagerLimit;

	protected $fillable = [
    'uuid',
    'number',
    'name',
    'color',
    'date_start',
    'date_end',
    'workflow',
    'company_id',
    'project_state_id',
    'user_id',
    'state_id',
    'vertec_id'
	];

  protected $casts = [
    'created_at' => 'datetime:d.m.Y H:i',
    'deleted_at' => 'datetime:d.m.Y H:i',
    'last_activity' => 'datetime:d.m.Y H:i',
  ];

  protected $appends = [
    'title',
    'iso_date_start',
    'iso_date_end',
    'workflow_uri',
  ];

  /**
   * Relations
   */

  public function company()
  {
    return $this->hasOne(Company::class, 'id', 'company_id',);
  }

  public function manager()
  {
    return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
  }

  public function state()
  {
    return $this->hasOne(ProjectState::class, 'id', 'project_state_id');
  }

	public function companies()
	{
		return $this->belongsToMany(Company::class);
	}

	public function users()
	{
		return $this->belongsToMany(User::class);
	}

  public function messages()
  {
    return $this->hasMany(Message::class, 'project_id', 'id')->orderBy('created_at', 'DESC');
  }

  public function quotes()
  {
    return $this->hasMany(ProjectQuote::class, 'project_id', 'id')->orderBy('created_at', 'DESC');
  }

  /**
   * Local scopes
   */

  public function scopeByCompanyUser($query, $companyId)
  {
    return $query->where('project_state_id', 1)->where('company_id', $companyId);
  }

  public function scopeActive($query)
  {
    return $query->where('project_state_id', 1);
  }

  public function scopeArchive($query)
  {
    return $query->where('project_state_id', 2);
  }

  /**
   * Mutator for date_start
   * @param Date $value
   */

  public function setDateStartAttribute($value)
  {
    $this->attributes['date_start'] = $value ? \Carbon\Carbon::parse($value)->format('Y.m.d') : NULL;
  }

  /**
   * Mutator for date_end
   * @param Date $value
   */

  public function setDateEndAttribute($value)
  {
    $this->attributes['date_end'] = $value ? \Carbon\Carbon::parse($value)->format('Y.m.d') : NULL;
  }

  /**
   * Accessor for date_start
   * @param Date $value
   * @return string
   */

  public function getDateStartAttribute($value)
  {
    return \Carbon\Carbon::parse($value)->format('d.m.Y');
    // return $value ? strftime('%d.%m.%Y', strtotime($value)) : '–';
  }

  /**
   * Accessor for date_end
   * @param Date $value
   */

  public function getDateEndAttribute($value)
  {
    return \Carbon\Carbon::parse($value)->format('d.m.Y');
    //return $value ? strftime('%d.%m.%Y', strtotime($value)) : '–';
  }

  /**
   * Accessor for iso_date_start
   * @param Date $value
   */

  public function getIsoDateStartAttribute($value)
  {
    return \Carbon\Carbon::parse($value)->toIso8601String();
  }

  /**
   * Accessor for iso_date_end
   * @param Date $value
   */

  public function getIsoDateEndAttribute($value)
  {
    return \Carbon\Carbon::parse($value)->toIso8601String();
  }
 
  /**
   * Get project title (Number + Name)
   *
   * @param  string  $value
   * @return string
   */
  public function getTitleAttribute($value)
  {
    return $this->number . ' ' . $this->name;
  }

  /**
   * Get a valid workflow uri
   *
   * @param  string  $value
   * @return string
   */
  public function getWorkflowUriAttribute($value)
  {
    if (!$this->workflow)
    {
      return;
    }
    
    $url = $this->workflow;
    if (!preg_match("~^(?:f|ht)tps?://~i", $url))
    {
      $url = "https://" . $url;
    }
    return $url;
  }

}
