<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Base
{
  use HasFactory;

  protected $table = 'project_user';

	protected $fillable = [
		'user_id',
		'project_id',
  ];

  /**
   * Relations
   */

	public function project()
	{
		return $this->belongsTo(Project::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

}
