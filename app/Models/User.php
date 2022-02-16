<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
  use Notifiable;
  
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
    'email_verified_at',
    'language_id',
    'company_id',
    'gender_id',
    'role_id'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token', 'role'
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

  /**
   * Role helper for admins
   */

  public function isAdmin()
  {
    return $this->role_id == 1 ? TRUE : FALSE;
  }

  /**
   * Role helper for admins
   */

  public function isEditor()
  {
    return $this->role_id == 2 ? TRUE : FALSE;
  }
}
