<?php
namespace App\Models;
use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class MessageFile extends Base
{
  use HasFactory, SoftDeletes;

	protected $fillable = [
    'uuid',
    'name',
    'original_name',
    'image_orientation',
    'image_ratio',
    'folder',
    'extension',
    'size',
    'preview',
    'message_id',
    'file_deleted_at',
	];

  /**
   * Relations
   */

  public function message()
  {
    return $this->belongsTo(Message::class);
  }

  public function markups()
  {
    return $this->hasMany(Markup::class);
  }

}
