<?php
namespace App\Models;
use App\Models\Base;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Notification extends Base
{
  use HasTranslations;

  /**
   * The model's default values for attributes.
   *
   * @var array
   */

  protected $attributes = [
    'title' => '{ "de": "null", "en": "null" }',
    'text'  => '{ "de": "null", "en": "null" }',
  ];

  public $translatable = [
    'title', 
    'text'
  ];

	protected $fillable = [
    'title',
    'text',
    'publish'
  ];

  /**
   * Scope for published notifications
   */
  
  public function scopePublished($query)
  {
    return $query->where('publish', 1);
  }
}
