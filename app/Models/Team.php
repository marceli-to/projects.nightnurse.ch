<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Base
{
  use HasFactory, SoftDeletes;

  public const TEAM_ZURICH = 1;
  public const TEAM_BUENOS_AIRES = 2;

	protected $fillable = [
    'uuid',
    'description',
    'company_id',
	];

  public function users()
  {
    return $this->hasMany(User::class)->orderBy('firstname');
  }
}