<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Base
{
  use HasFactory, SoftDeletes;

	protected $fillable = [
    'uuid',
    'name',
    'city',
    'owner',
    'publish',
	];

  /**
   * Relations
   */

  public function users()
  {
    return $this->hasMany(User::class, 'company_id', 'id');
  }

	public function projects()
	{
		return $this->belongsToMany(Project::class);
	}


	/**
   * Scope for clients (external companies)
   */

	public function scopeClients($query)
	{
		return $query->where('owner', 0);
	}

	public function scopeOwner($query)
	{
		return $query->where('owner', 1);
	}

}
