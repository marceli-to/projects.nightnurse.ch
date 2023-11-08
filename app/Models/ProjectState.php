<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectState extends Base
{
  use HasFactory;
  
	protected $fillable = [
    'description',
    'position'
	];
}
