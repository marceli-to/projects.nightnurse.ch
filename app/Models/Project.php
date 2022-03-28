<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Base
{
  use HasFactory, SoftDeletes;

	protected $fillable = [
    'uuid',
    'number',
    'name',
    'date_start',
    'date_end',
    'company_id',
    'project_state_id',
    'user_id',
    'state_id'
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
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public function state()
  {
    return $this->hasOne(ProjectState::class, 'id', 'state_id');
  }

	public function companies()
	{
		return $this->belongsToMany(Company::class);
	}

  public function messages()
  {
    return $this->hasMany(Message::class, 'project_id', 'id');
  }

  /**
   * Local scopes
   */

  public function scopeByCompanyUser($query, $companyId)
  {
    return $query->where('project_state_id', 1)->where('company_id', $companyId);
  }

  /**
   * Accessor for date_start
   * @param Date $value
   */

  public function setDateStartAttribute($value)
  {
    $this->attributes['date_start'] = date('Y.m.d', strtotime($value));
  }

  /**
   * Accessor for date_end
   * @param Date $value
   */

  public function setDateEndAttribute($value)
  {
    $this->attributes['date_end'] = date('Y.m.d', strtotime($value));
  }

  /**
   * Mutator for date_start
   * @param Date $value
   */

  public function getDateStartAttribute($value)
  {
    return $value ? strftime('%d.%m.%Y', strtotime($value)) : null;
  }

  /**
   * Mutator for date_end
   * @param Date $value
   */

  public function getDateEndAttribute($value)
  {
    return $value ? strftime('%d.%m.%Y', strtotime($value)) : null;
  }
}
