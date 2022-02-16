<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Base
{
  use HasFactory;

	protected $fillable = [
    'description',
	];
}
