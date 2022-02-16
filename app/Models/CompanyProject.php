<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProject extends Base
{
  use HasFactory;

  protected $table = 'company_project';

	protected $fillable = [
		'company_id',
		'project_id',
  ];

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function project()
	{
		return $this->belongsTo(Project::class);
	}

}
