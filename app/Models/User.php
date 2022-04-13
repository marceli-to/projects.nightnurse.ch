<?php
namespace App\Models;
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
    'gender_id',
    'role_id',
    'vertec_id'
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
  protected $appends = ['short_name', 'full_name'];


  public function permissions()
  {
    if ($this->role_id == 1)
    {
      return [
        'private' => TRUE,
        'clients' => TRUE
      ];
    }

    return [];
  }


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

  public function gender()
  {
    return $this->hasOne(Gender::class, 'id', 'gend_id');
  }

	public function messages()
	{
		return $this->belongsToMany(Message::class);
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
    return $this->role_id == 1 ? TRUE : FALSE;
  }

  /**
   * Role helper for editors
   */

  public function isEditor()
  {
    return $this->role_id == 2 ? TRUE : FALSE;
  }

  /**
   * Get the user's full name.
   *
   * @param  string  $value
   * @return string
   */
  public function getFullNameAttribute($value)
  {
    return $this->firstname . ' ' . $this->name;
  }

  /**
   * Get the user's short name.
   *
   * @param  string  $value
   * @return string
   */
  public function getShortNameAttribute($value)
  {
    return $this->firstname . ' ' . substr($this->name, 0,1) . '.';
  }
}
