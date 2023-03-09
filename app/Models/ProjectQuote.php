<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectQuote extends Base
{
  use HasFactory;

	protected $fillable = [
    'uuid',
    'description',
    'uri',
    'project_id',
	];

  /**
   * Relations
   */

  public function project()
  {
    return $this->belongsTo(Project::class);
  }
}
