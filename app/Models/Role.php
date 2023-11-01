<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Base
{
  use HasFactory;

  const ADMIN = 1;
  const CLIENT = 2;
  const CLIENT_ADMIN = 3;

	protected $fillable = [
    'description',
	];

  public function isAdmin()
  {
    return $this->id === self::ADMIN;
  }

  public function isClient()
  {
    return $this->id === self::CLIENT || $this->id === self::CLIENT_ADMIN;
  }

  public function isClientAdmin()
  {
    return $this->id === self::CLIENT_ADMIN;
  }
}
