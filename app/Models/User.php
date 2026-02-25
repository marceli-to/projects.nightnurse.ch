<?php
namespace App\Models;
use App\Models\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
  use Notifiable, SoftDeletes;
  
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */

  protected $fillable = [
    'uuid',
    'firstname', 
    'name', 
    'email',
    'phone',
    'password', 
    'api_token',
    'email_verified_at',
    'register_complete',
    'language_id',
    'company_id',
    'team_id',
    'gender_id',
    'role_id',
    'vertec_id',
    'is_sbb',
    'send_copy'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token', 'api_token'
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = ['short_name', 'full_name', 'acronym'];


  /**
   * Relations
   */

  public function role()
  {
    return $this->hasOne(Role::class, 'id', 'role_id');
  }

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function team()
  {
    return $this->belongsTo(Team::class);
  }

  public function gender()
  {
    return $this->hasOne(Gender::class, 'id', 'gend_id');
  }

	public function messages()
	{
		return $this->belongsToMany(Message::class);
	}

	public function projects()
	{
		return $this->belongsToMany(Project::class);
	}

	/**
   * Scope for staff (members of company 'Nightnurse')
   */

	public function scopeStaff($query)
	{
		return $query->where('company_id', config('client.owner_id'))->where('register_complete', 1);
	}

	/**
   * Scope for registered users
   */

	public function scopeRegistered($query)
	{
		return $query->where('register_complete', 1);
	}

  /**
   * Role helper for admins
   */

  public function isAdmin()
  {
    return Role::find($this->role_id)->isAdmin();
  }

  /**
   * Role helper for clients
   */

  public function isClient()
  {
    return Role::find($this->role_id)->isClient();
  }

  /**
   * Role helper for client admins
   */

  public function isClientAdmin()
  {
    return Role::find($this->role_id)->isClientAdmin();
  }

  /**
   * SBB helper
   */

  public function isSBB()
  {
    return $this->is_sbb;
  }

  /**
   * Get the user's full name.
   *
   * @param  string  $value
   * @return string
   */
  public function getFullNameAttribute($value)
  {
    if ($this->firstname && $this->name)
    {
      return "{$this->firstname} {$this->name}";
    }
    return $this->email;
  }

  /**
   * Get the user's short name.
   *
   * @param  string  $value
   * @return string
   */
  public function getShortNameAttribute($value)
  {
    return $this->firstname . ' ' . mb_substr($this->name, 0,1) . '.';
  }

  /**
   * Get the user's acronym.
   *
   * @param  string  $value
   * @return string
   */

  public function getAcronymAttribute($value)
  {
    return mb_substr($this->firstname, 0,1) . mb_substr($this->name, 0,1);
  }
}
